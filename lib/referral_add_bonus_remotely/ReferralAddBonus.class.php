<?php

OTBase::import('system.lib.referral_add_bonus_remotely.Entity.AddBonusInfo');
OTBase::import('system.lib.referral_add_bonus_remotely.Entity.RemoveBonusInfo');
OTBase::import('system.lib.referral_system.lib.*');
OTBase::import('system.lib.Lang');

class ReferralAddBonus {
    private $sid = '';
    private $translations = array();

    /**
     * @var OTAPIlib
     */
    protected $otapilib;

    /**
     * @var CMS
     */
    protected $cms;

    /**
     * @var RequestWrapper
     */
    protected $request;

    /**
     * @var ReferralOrderManager
     */
    protected $referralOrderManager;

    /**
     * @var ReferralUserManager
     */
    protected $referralUserManager;

    /**
     * @var ReferralCategoryManager
     */
    protected $referralCategoryManager;

    /**
     * @var ReferralLogManager
     */
    protected $log;

    /**
     * Конструктор класса.
     *
     */
    public function __construct() {
        $this->otapilib = new OTAPIlib();
        $this->cms = new CMS();
        $this->cms->Check();

        $this->request = new RequestWrapper();

        $this->referralOrderManager = new ReferralOrderManager($this->cms);
        $this->referralUserManager = new ReferralUserManager($this->cms);
        $this->referralCategoryManager = new ReferralCategoryManager($this->cms);

        $this->translations = $this->getTranslationsAction();
        $this->log = new ReferralLogManager($this->cms);
    }

    public function SetRequest($request){
        $this->request = $request;
    }

    /**
     *
     */
    public function runAction(){
        $orderId = $this->cms->escape($this->request->getValue('order_id')); // id заказа
        $userId  = (int)$this->request->getValue('user_id');  // id пользователя в системе БЛ
        $amount  = (float)$this->request->getValue('amount'); // сумма, которая поступила от реферала

        $this->checkOrderExists($orderId, $userId, $amount);
        $referral = $this->getReferralUser($orderId, $userId, $amount);
        $referrer = $this->getReferrerUser($referral, $orderId, $userId, $amount);
        $referrerCategory = $this->getReferrerCategory($referrer, $orderId, $userId, $amount);

        $profitPercent = $referrerCategory->GetProfitPercent();
        $addAmount = $amount * $profitPercent;

        $this->referralOrderManager->Add($orderId, $userId, $referrer->GetId(), $amount, $profitPercent);
        
        
        if ($referrer->GetId() == 0) {
            // если нет рефера, некому и бонус давать
            return false;
        }

        // зачисляем бонус, уведомляем реферера о бонусе
        // даже если $addAmount = 0 отправляем ответ для БЛ, для успешного вызова referral_add_bonus_confirm.php
        $comment = Lang::get('referral_bonus') . '. ' . Lang::get('Order') . ' #'. $orderId
            . ' ' . Lang::get('from_referral') . ' № ' . $userId
            . ', ' . ($profitPercent * 100) . '% ' . Lang::get('of_amount') . ' ' . $amount ;
        $this->log->Add('add_amount', 'sid: ' . $this->sid
            . '; order: '.$orderId.'; parent_id: '.$referrer->GetId()
            .'; add_amount: '.$addAmount.'; comment'.$comment.';');

        $Response = new AddBonusInfo($referrer->GetId(), $addAmount, $comment);
        return $Response->toXML();
    }

    private function checkOrderExists($orderId, $userId, $amount){
        if ($this->referralOrderManager->GetById($orderId)) {
            // заказ с данным id уже существует в системе
            $this->log->Add('order_exist', 'order: '.$orderId.'; userId: '.$userId.'; amount: '.$amount.';');
            throw new Exception('order exist');
        }
    }

    private function getReferralUser($orderId, $userId, $amount){
        try{
            return $this->referralUserManager->GetById($userId);
        }
        catch(NotFoundException $e){
            // добавляем в таблицу информацию о заказе, но с пометкой что нет реферера
            $this->referralOrderManager->Add($orderId, $userId, 0, $amount, 0);
            $this->log->Add('referral_not_found', 'order: '.$orderId.'; userId: '.$userId.'; amount: '.$amount.';');
            throw new Exception('referral_not_found');
        }
    }

    private function getReferrerUser($referral, $orderId, $userId, $amount){
        try{
            return $this->referralUserManager->GetParent($referral);
        }
        catch(NotFoundException $e){
            $this->log->Add('referrer_not_found', 'order: '.$orderId.'; userId: '.$userId.'; amount: '.$amount.';');
            throw new Exception('referrer_not_found');
        }
    }

    public function getReferrerCategory($referrer, $orderId, $userId, $amount){
        try{
            return $this->referralUserManager->GetCategoryInfo($referrer);
        }
        catch(NotFoundException $e){
            $this->log->Add('category_for_referrer_not_exists', 'order: '.$orderId.'; userId: '.$userId.'; amount: '.$amount.';');
            throw new Exception('category_for_referrer_not_exists');
        }
    }

    public function confirmAction(){
        $orderId = $this->cms->escape($this->request->getValue('order_id')); // id заказа
        $order = $this->referralOrderManager->GetById($orderId);
        $allBonusAmount = $this->referralOrderManager->CalculateAmountUser($order->GetParentId());
        $referrer = $this->referralUserManager->GetById($order->GetParentId());
        $referral = $this->referralUserManager->GetById($order->GetUserId());
        $referrerCategory = $this->referralUserManager->GetCategoryInfo($referrer);

        $addAmount = $referrerCategory->GetProfitPercent()*$order->GetAmount();
        $balance = $referrer->GetBalance();
        $referrer->SetBalance($balance + $addAmount);
        $this->referralUserManager->Save($referrer);
        $this->log->Add('set_balance', 'referer_id: ' . $referrer->GetId()
            . '; balance: '.($balance + $addAmount).';');

        if ($addAmount > 0) {
            // отправляем уведомление рефереру о получении бонуса от реферала
            $subject = (isset($this->translations['subject_add_bonus'])) ? $this->translations['subject_add_bonus'] : '';
            $message = $this->getMailTemplatesAction('mail_add_bonus');
            $key = Array('{{login}}', '{{percent}}', '{{name_referal}}', '{{add_bonus}}');
            $value = Array($referrer->GetLogin(),
                ($referrerCategory->GetProfitPercent() * 100),
                $referral->GetLogin(),
                $addAmount);
            $message = str_replace($key, $value, $message);
            $this->referralUserManager->SendMessageToPrivateOffice($referrer->GetId(), $subject, $message);
        }

        // найти к какой категории должен принаждежать пользователь после начисления бонуса
        $expectedCategoryId = $this->referralCategoryManager->PickUpCategoryIdByAmount($allBonusAmount);
        $currentCategory = $referrer->GetCategory();

        // если категория изменилась
        if ($currentCategory != $expectedCategoryId) {
            // поменять категорию
            $referrer->SetCategory($expectedCategoryId);
            $this->referralUserManager->Save($referrer);
            $this->log->Add('set_category', 'referrer_id: ' . $referrer->GetId()
                . '; old_category: '.$currentCategory
                . '; new_category: '.$expectedCategoryId.';');

            // сообщить в ЛК и на почту

            $subject = (isset($this->translations['subject_change_category'])) ? $this->translations['subject_change_category'] : '';
            $message = $this->getMailTemplatesAction('mail_change_category');
            $key = Array('{{login}}', '{{new_category}}', '{{new_percent}}');
            $value = Array($referrer->GetLogin(),
                $referrerCategory->GetGroupName(),
                ($referrerCategory->GetProfitPercent() * 100));
            $message = str_replace($key, $value, $message);
            $this->referralUserManager->SendMessageToPrivateOffice($referrer->GetId(), $subject, $message);
        }

        return 'Ok';
    }

    public function cancelAction(){
        $orderId = $this->cms->escape($this->request->getValue('order_id')); // id заказа
        $this->referralOrderManager->Delete($orderId);
        return 'Ok';
    }

    public function denyAction(){
        $orderId = $this->cms->escape($this->request->getValue('order_id')); // id заказа
        $userId  = (int)$this->request->getValue('user_id');  // id пользователя в системе БЛ

        $order = $this->referralOrderManager->GetById($orderId);
        $cancelAmount = 0;
        if ($order) {
            $cancelAmount  = $order->GetAmount()*$order->GetProfitPercent(); // сумма, которая поступила от реферала
            $referrer = $this->referralUserManager->GetById($order->GetParentId());

            if ($cancelAmount == 0) {
                $this->log->Add('deny_amount_is_zero', 'order: '.$orderId.'; userId: '.$userId.';');
                $comment = 'deny_amount_is_zero ' . $orderId . ' from referral N ' . $userId;
                $Response = new RemoveBonusInfo($order->GetParentId(), $cancelAmount, $comment);
            } else {
                $comment = 'referral deny bonus. order ' . $orderId
                    . ' from referral N ' . $userId
                    . ', ' . ($order->GetProfitPercent() * 100) . '% of amount ' . $order->GetAmount() ;
                $this->log->Add('deny_amount', 'sid: ' . $this->sid
                    . '; order: '.$orderId.'; parent_id: '.$referrer->GetId()
                    .'; deny_amount: '.$cancelAmount.'; comment'.$comment.';');

                $Response = new RemoveBonusInfo($order->GetParentId(), $cancelAmount, $comment);                
            }
        } else {
            $this->log->Add('unknown order', 'order: '.$orderId.'; userId: '.$userId.';');
            $comment = 'unknown order ' . $orderId . ' from referral N ' . $userId;
            $Response = new RemoveBonusInfo(0, $cancelAmount, $comment);
        }
        return $Response->toXML();
    }

    public function denyConfirmAction(){
        $orderId = $this->cms->escape($this->request->getValue('order_id')); // id заказа

        $order = $this->referralOrderManager->GetById($orderId);
        $cancelAmount  = $order->GetAmount()*$order->GetProfitPercent(); // сумма, которая поступила от реферала
        $referrer = $this->referralUserManager->GetById($order->GetParentId());
        $referrer->SetBalance($referrer->GetBalance() - $cancelAmount);
        $this->referralUserManager->Save($referrer);
        $this->referralOrderManager->Delete($orderId);
        $this->log->Add('deny_amount_confirm', 'sid: ' . $this->sid
            . '; order: '.$orderId.'; parent_id: '.$referrer->GetId()
            .'; deny_amount: '.$cancelAmount.';');

        $allBonusAmount = $this->referralOrderManager->CalculateAmountUser($order->GetParentId());
        $expectedCategoryId = $this->referralCategoryManager->PickUpCategoryIdByAmount($allBonusAmount);
        $currentCategory = $referrer->GetCategory();

        if ($currentCategory != $expectedCategoryId) {
            // поменять категорию
            $referrer->SetCategory($expectedCategoryId);
            $this->referralUserManager->Save($referrer);
            $this->log->Add('set_category', 'referrer_id: ' . $referrer->GetId()
                . '; old_category: '.$currentCategory
                . '; new_category: '.$expectedCategoryId.';');

            // сообщить в ЛК и на почту

            $subject = (isset($this->translations['subject_change_category'])) ? $this->translations['subject_change_category'] : '';
            $message = $this->getMailTemplatesAction('mail_change_category');
            $key = Array('{{login}}', '{{new_category}}', '{{new_percent}}');
            $value = Array($referrer->GetLogin(),
                $this->referralUserManager->GetCategoryInfo($referrer)->GetGroupName(),
                ($this->referralUserManager->GetCategoryInfo($referrer)->GetProfitPercent() * 100));
            $message = str_replace($key, $value, $message);
            $this->referralUserManager->SendMessageToPrivateOffice($referrer->GetId(), $subject, $message);
        }

        return 'Ok';
    }

    /**
     * Получить переводы
     * @return array()
     */
    public function getTranslationsAction() {
        $translations = Array();
        $lang = Session::getActiveLang();

        $langFilePath = (dirname(__FILE__) . '/mails/'.$lang.'.xml');
        if(file_exists($langFilePath)) {
            $translations_xml = simplexml_load_file($langFilePath);
            foreach($translations_xml->key as $t){
                $translations[(string)$t['name']] = (string)$t;
            }

            return $translations;
        }
        return false;
    }

    /**
     * Получить шаблон письма
     * @param $name_templates
     * @return string
     */
    public function getMailTemplatesAction($name_templates) {
        $lang = Session::getActiveLang();
        $mailFilePath = (dirname(__FILE__) . '/mails/' . $name_templates . '_'.$lang.'.html');
        if(file_exists($mailFilePath)) {
            return file_get_contents($mailFilePath);
        }
        return '';
    }
}