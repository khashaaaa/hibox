<?php

class Pay extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'pay'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/pay/';

    private $currencyData = array();

    /**
     * @var CMS
     */
    protected $cms;

    public function __construct()
    {
        $this->cms = new CMS();
        $this->cms->Check();
        parent::__construct(true);
    }

    protected function setVars()
    {
        if(!Session::getUserData()){
            header('Location: /?p=login');
        }

        $currency_list = InstanceProvider::getObject()->getCurrencyInstanceList();
        $this->currencyData = $this->otapilib->GetCurrencyInstanceList($currency_list->asXML());
        if ($this->currencyData) {
            $this->currencyData = $this->currencyData['Internal'];
        }
        $this->_pay();
    }

    private function paymentXML($Amount, $CurrencyCode, $PaymentSystemId, $OrderId, $SuccessUrl, $FailUrl,
                                $IsPartialPayment){
        $xml = new SimpleXMLElement('<PaymentRequest></PaymentRequest>');
        $xml->addChild('Amount', htmlspecialchars($Amount));
        $xml->addChild('CurrencyCode', $CurrencyCode);
        $xml->addChild('PaymentSystemId', htmlspecialchars($PaymentSystemId));
        if(@$OrderId) $xml->addChild('OrderId', htmlspecialchars($OrderId));
        $xml->addChild('SuccessUrl', htmlspecialchars($SuccessUrl));
        $xml->addChild('FailUrl', htmlspecialchars($FailUrl));
        $xml->addChild('IsPartialPayment', htmlspecialchars($IsPartialPayment));

        if(htmlspecialchars(@$_GET['Custom'][$PaymentSystemId])){
            $xml->addChild('Custom', htmlspecialchars($_GET['Custom'][$PaymentSystemId]));
        }
        return $xml->asXML();
    }

    private function _getUrls($isOrder, $isDeposit, $isArca){
        if($isOrder){
            $successUrl = defined('CFG_ORDER_PAID_SUCCESS_URL') ? CFG_ORDER_PAID_SUCCESS_URL :
                UrlGenerator::getHomeUrl() .'/?p=paymentsuccess';

            $failureUrl = defined('CFG_ORDER_PAID_FAIL_URL') ? CFG_ORDER_PAID_FAIL_URL :
                UrlGenerator::getHomeUrl() .'/?p=paymenterror';
        }
        if($isDeposit){
            $successUrl = defined('CFG_DEPOSIT_PAID_SUCCESS_URL') ? CFG_DEPOSIT_PAID_SUCCESS_URL :
                UrlGenerator::getHomeUrl() .'/?p=depositsuccess';

            $failureUrl = defined('CFG_DEPOSIT_PAID_FAIL_URL') ? CFG_DEPOSIT_PAID_FAIL_URL :
                UrlGenerator::getHomeUrl() .'/?p=depositfail';
        }
        if($isArca){
            $successUrl = UrlGenerator::getHomeUrl() .'/lib/arca/handlers/success.php';
            $failureUrl = UrlGenerator::getHomeUrl() .'/lib/arca/handlers/fail.php';
        }
        return array($successUrl, $failureUrl);
    }

    private function _sberbankPay($sid){
        global $otapilib;
        $this->_template='sberbank';
        $data = $this->cms->getSiteConfig();
        if ($data[0]){
            $data[1]['INN_of_payee']=str_split($data[1]['INN_of_payee'],1);
            $data[1]['account_number_of_payee']=str_split($data[1]['account_number_of_payee'],1);
            $data[1]['bank_identification_code']=str_split($data[1]['bank_identification_code'],1);
            $data[1]['correspondent_bank_account']=str_split($data[1]['correspondent_bank_account'],1);
            $this->tpl->assign('quittanceData', $data[1]);
            if (CFG_MULTI_CURL)
            {
                $otapilib->InitMulti();
                $otapilib->GetAccountInfo($sid);
                $otapilib->GetUserInfo($sid);
                $otapilib->MultiDo();
                $accountinfo = $otapilib->GetAccountInfo($sid);
                $userinfo = $otapilib->GetUserInfo($sid);
                $otapilib->StopMulti();
            } else {
                $accountinfo = $otapilib->GetAccountInfo($sid);
                $userinfo = $otapilib->GetUserInfo($sid);
            }
            $this->tpl->assign('accountinfo', $accountinfo);
            $this->tpl->assign('userInfo', $userinfo);

            if(isset($_GET['orderid'])){
                $order_info = $otapilib->GetSalesOrderDetails($sid, $_GET['orderid']);
                $t_amount = (float)$order_info['salesorderinfo']['totalamount'];
            }
            $this->tpl->assign('money', isset($_GET['orderid']) ? $t_amount : $_GET['money']);
        }
        else{
            $this->tpl->assign('quittanceData', array());
        }
    }

    private function _getPayForm($sid, $orderId, $paymentId, $cur){
        global $otapilib;

        $this->_template = 'pay_form';
        $pid = $paymentId;

        $isArca = substr($paymentId,0 ,4) == 'arca';
        $isOrder = isset($orderId) && !$isArca;
        $isDeposit = !isset($orderId) && !$isArca;
        list($successUrl, $failureUrl) = $this->_getUrls($isOrder, $isDeposit, $isArca);

        if ($pid=='sberbank'){
            return $this->_sberbankPay($sid);
        }

        if($orderId){
            $order_info = $otapilib->GetSalesOrderDetails($sid, $orderId);
            $t_amount = (float)$order_info['salesorderinfo']['totalamount'];
        }
        else{
            $t_amount = (float)$_GET['money'];
        }

        $amount = number_format($t_amount, 2, '.', '');
        $xml = $this->paymentXML($amount, $cur, $pid, $orderId, $successUrl,  $failureUrl, 'false');
        try {
            $form = $otapilib->GetPaymentParameters(Session::getUserDataSid(), $xml);
        } catch (Exception $e) {
            $form = '';
            $this->tpl->assign('payment_error', $e->getMessage());
        }

        if($isArca && $form !== false){
            $form['Parameters'] = array_map(array($this, '_formatAmount'), $form['Parameters']);

            $A = new Arca();
            $result = $A->savePayment($form, $paymentId);
            if(!$result[0]) $this->tpl->assign('payment_error', $result[1]);
        }
        $this->tpl->assign('form', $form);

        return $form;
    }

    private function _getPayModes(){
        global $otapilib;
		
        $methods = $otapilib->GetPaymentModes();
        $currency = $otapilib->GetInstanceCurrencyInfoList();
        $methods['sberbank'] = CMS::GetQuittanceMethod($currency['Internal']);
        if (empty ($methods['sberbank'])) unset ($methods['sberbank']);
        if($otapilib->error_code == 'NotFound' && !$methods){
            $this->tpl->assign('methods_error', Lang::get('ps_not_available'));
        }
        else{
            $method_groups = array();
            if(General::getConfigValue('payment_in_cash')){
				$method_groups_new = Plugins::invokeEvent('showPayCash');
				
				if ($method_groups_new) {
					$method_groups[Lang::get('payment_in_cash')][] = $method_groups_new;
				} else {
					$method_groups[Lang::get('cash')][] = array(
                    'Id' => "payment_in_cash",
                    'Name' =>Lang::get('payment_in_cash'),
                    'Description' => Lang::get('payment_in_cash_description'),
                    'PaymSortCode' => "Cash",
                    'PaymSortText' => Lang::get('cash'),
                    'ImageURL' =>"",
                    'PaymentSystem' =>"",
                    'CustomField' =>"None",
                    'customfield' =>"None"
                	);
				}
                
            }
            foreach ($methods as $method) {
                $group_key   = $method['paymsorttext'];

                if($group_key == '' || !$group_key)
                    $group_key = Lang::get('other_payments');
                $method_groups[$group_key][] = $method;
            }

            // Reordering other group in the end
            if(isset($method_groups[Lang::get('other_payments')])){
                $other_methods = $method_groups[Lang::get('other_payments')];
                unset($method_groups[Lang::get('other_payments')]);
                $method_groups[Lang::get('other_payments')] = $other_methods;
            }

            $newMethods = Plugins::invokeEvent('onPaymentMethodsOut', array('methods' => $methods));
            $newMethodGroups = Plugins::invokeEvent('onPaymentMethodGroupsOut', array('methodGroups' => $method_groups));

            $this->tpl->assign('method_groups', is_array($newMethodGroups) ? $newMethodGroups : $method_groups);
            $this->tpl->assign('methods', is_array($newMethods) ? $newMethods : $methods);

        }
        $this->tpl->assign('currencySign', $this->currencyData ? $this->currencyData['Sign'] : '');
        $this->tpl->assign('enter_money', 1);

        return true;
    }

    /*
     * оплата наличными со страницы заказа
     */
    private function _payCashInOrder($sid, $orderId, $user, $paymentSum) {
        $orderInfo = $this->ordersProxy->GetSalesOrderDetails($sid, $orderId);
        $paymentSum = $orderInfo['salesorderinfo']['remainamount'];

        // отправляем письмо админу
        Notifier::notifyAdminOnPaymentInCash($orderInfo['SalesOrderInfo'], $user, $paymentSum);

        // создаем новый тикет в службе поддержки сайта с категорией "Оплата наличными" и с текстом описания заявки
        $categoryId = $orderInfo['SalesLinesList'][0]['CategoryId'];
        $paymentSumText = (string)(number_format(
            (float)$orderInfo['salesorderinfo']['remainamount'],
            (int)General::getNumConfigValue('price_rounding'), '.', ' '
        ) . ' ' . $orderInfo['salesorderinfo']['currencysign']);
        $tichetMessage = Lang::get('payment_in_cash_text').'. '.Lang::get('sum').': '.$paymentSumText;
        return $this->_payCashCreateTicketSupport((int) $user['id'], $user['Login'], $orderId, $categoryId, $tichetMessage);
    }

    /*
     * оплата наличными из личного кабинета
     */
    private function _payCashInPrivateOffice($user, $paymentSum) {
        $orderId = '';
        $categoryId = '';
        $paymentSumText = (string)(number_format(
            (float)$paymentSum,
            (int)General::getNumConfigValue('price_rounding'), '.', ' '
        ) . ' ' . $this->currencyData['Sign']);

        // отправляем письмо админу
        $data = Array();
        $data['user'] = $user;
        $data['paymentSum'] = $paymentSum;
        $data['payment_sum_text'] = $paymentSumText;

        Notifier::generalNotification('payment_in_cash', Lang::get('payment_in_cash'), $data);

        // создаем новый тикет в службе поддержки сайта с категорией "Оплата наличными" и с текстом описания заявки
        $tichetMessage = Lang::get('payment_in_cash_text').'. '.Lang::get('sum').': '.$paymentSumText;
        return $this->_payCashCreateTicketSupport((int) $user['id'], $user['Login'], '', '', $tichetMessage);
    }

    /*
     * оплата наличными из личного кабинета
     */
    private function _payCashCreateTicketSupport($userId, $userLogin, $orderId, $categoryId, $tichetMessage) {
        // создаем новый тикет в службе поддержки сайта с категорией "Оплата наличными" и с текстом описания заявки
        $supportRepository = new SupportRepository(new CMS());

        $ticketId = $supportRepository->createTicket($userId, $orderId, $categoryId, Lang::get('payment_in_cash'), $tichetMessage, false, $userLogin);
        if ($ticketId) {
            // перенаправить клиента на созданный тикет в службе поддержки
            header('Location: ' . UrlGenerator::generateSupportUrl(array('mode' => 'chat', 'id' => $ticketId)));
            die();
        }

        return false;
    }

    private function _pay() {
        $sid = Session::getUserSession();
        $user = $this->otapilib->GetUserInfo($sid);		

        $orderId = $this->request->getValue('orderid') ? $this->request->getValue('orderid') : $this->request->getValue('salesId');
        $paymentId = $this->request->getValue('paymentId');
        if ($this->currencyData) {
            $currency = $this->currencyData['Code'];
        }

        if ($paymentId) {
			$userData = new UserData();
			$userData->ClearAccountInfoCache();
            if ($paymentId == "payment_in_cash") { // If payment in cash
                Plugins::invokeEvent('onPayCash');
                if ($orderId) { // оплата наличными со страницы заказа
                    $response = $this->_payCashInOrder($sid, $orderId, $user, '');
                } else { // оплата наличными из личного кабинета 
                    $response = $this->_payCashInPrivateOffice($user, $this->request->getValue('money'));
                }
                // заглушка
                if (!$response)
                    header('Location: ' . UrlGenerator::generateContentUrl('cash_payment'));
            } else {
                $form = $this->_getPayForm($sid, $orderId, $paymentId, $currency);
                if (SCRIPT_NAME == 'pay_form_json') {
                    $this->_template = 'pay_form_json';
                    $this->tpl->assign('form', $form);
                }
            }
        } elseif ($orderId || $this->request->getValue('money') || $this->request->getValue('p') == 'pay') {

            return $this->_getPayModes();
        }
    }

    private function _formatAmount($p){
        if($p['Name'] == 'amount'){
            $p['Value'] = number_format($p['Value'], 2, '.', '');
            $p['value'] = number_format($p['Value'], 2, '.', '');
        }

        return $p;
    }
}
