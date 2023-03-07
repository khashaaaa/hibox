<?php

class Referrals
{

    private $pid;

    // массив всех рефералов в виде дерева
    private $referrals = array();

    // массив всех рефералов, ключ - id-реферала
    private $refdata = array();

    // массив рефералов для вывода на страницу
    private $showdata = array(-1);

    // статусы рефералов
    private $statuses = array(
        '-'
    , 'Участник'
    , 'Лидер'
    , 'Босс'
    , 'Президент'
    );

    /**
     * @var CMS
     */
    private $cms;


    // опеределяем от кого выбирать дерево
    function __construct()
    {
        $this->pid = isset($_GET['pid']) ? (int)$_GET['pid'] : 0;
        $this->cms = new CMS();
        $this->cms->Check();
    }

    function defaultAction($request)
    {
        global $otapilib;

        if (!Login::auth()) {
            include(TPL_DIR . 'login.php');
            return false;
        }

        $cms = new CMS();

        $status = $cms->Check();
        if (!$status) {
            include(TPL_DIR . 'db_connection_fail.php');
            die;
        }

        $cms->checkTable('site_referrals');

        $this->_getReferrals($cms->GetReferralUsers());
        $chain = $this->_getChain();

        $referrals = $this->getReferralsTree(array($this->pid));
        $currentUser = $this->getReferralUser($this->pid);
        $currentUserStatus = intval(@$currentUser['status']);

        $current_lang = $this->setActiveLang();
        $translations = $cms->getTranslations('', $current_lang);
        include(TPL_DIR . 'referrals/index.php');

    }

    public function getReferralUser($userId){
        $cms = new CMS();
        $cms->Check();
        $q = mysql_query("
            SELECT *
            FROM  `site_referrals` `r`
            WHERE
              `r`.`id` = $userId
        ");
        return mysql_fetch_assoc($q);
    }

    public function getReferralsTree($parentsId){
        $referralsIds = array();
        $referrals['list'] = $this->cms->GetChildrenReferralsInfo($parentsId);
        foreach ($referrals['list'] as &$r) {
            $referralsIds[] = $r['idx'];
            $r['presents_from_children'] = $this->getPresentsToUser($r['idx']);
            $r['present_exists_to_parent'] = $this->getPresentsFromUser($r['idx']);
            $r['present_exists_to_parent_sent'] = $this->getSentPresentsFromUser($r['idx']);
        }
        if($referralsIds)
            $referrals['children'] = $this->getReferralsTree($referralsIds);
        return $referrals;
    }

    public function getPresentsToUser($userId){
        $cms = new CMS();
        $cms->Check();
        $presents = mysql_query("
            SELECT COUNT(*)
            FROM `site_referrals_presents` `p`
              INNER JOIN `site_orders` `o` ON `p`.`order_id` = `o`.`order_id`
              INNER JOIN `site_referrals` `r` ON `r`.`id` = `o`.`referral_id`
            WHERE
              `p`.`sent` = 0 AND `r`.`parent_id` = $userId
        ");
        return mysql_result($presents, 0);
    }

    public function getPresentsFromUser($userId){
        $cms = new CMS();
        $cms->Check();
        $presents = mysql_query("
            SELECT COUNT(*)
            FROM `site_referrals_presents` `p`
              INNER JOIN `site_orders` `o` ON `p`.`order_id` = `o`.`order_id`
              INNER JOIN `site_referrals` `r` ON `r`.`id` = `o`.`referral_id`
            WHERE
              `p`.`sent` = 0 AND `r`.`id` = $userId
        ");
        return mysql_result($presents, 0);
    }

    public function getSentPresentsFromUser($userId){
        $cms = new CMS();
        $cms->Check();
        $presents = mysql_query("
            SELECT COUNT(*)
            FROM `site_referrals_presents` `p`
              INNER JOIN `site_orders` `o` ON `p`.`order_id` = `o`.`order_id`
              INNER JOIN `site_referrals` `r` ON `r`.`id` = `o`.`referral_id`
            WHERE
              `p`.`sent` = 1 AND `r`.`id` = $userId
        ");
        return mysql_result($presents, 0);
    }

    public function getOrderForPresent($userId){
        $cms = new CMS();
        $cms->Check();
        $presents = mysql_query("
            SELECT `o`.`order_id`
            FROM `site_referrals_presents` `p`
              INNER JOIN `site_orders` `o` ON `p`.`order_id` = `o`.`order_id`
              INNER JOIN `site_referrals` `r` ON `r`.`id` = `o`.`referral_id`
            WHERE
              `p`.`sent` = 0 AND `r`.`id` = $userId
        ");
        return mysql_result($presents, 0);
    }

    public function getParentUserForOrder($orderId){
        $cms = new CMS();
        $cms->Check();
        $presents = mysql_query("
            SELECT `rp`.`login`
            FROM `site_referrals_presents` `p`
              INNER JOIN `site_orders` `o` ON `p`.`order_id` = `o`.`order_id`
              INNER JOIN `site_referrals` `r` ON `r`.`id` = `o`.`referral_id`
              INNER JOIN `site_referrals` `rp` ON `r`.`parent_id` = `rp`.`id`
            WHERE
              `o`.`order_id` = $orderId
        ");
        return mysql_result($presents, 0);
    }

    public function updateOrderForPresent($orderId){
        $cms = new CMS();
        $cms->Check();
        return mysql_query("UPDATE `site_referrals_presents` SET `sent`=1 WHERE `order_id`=$orderId");
    }

    private function setActiveLang()
    {
        if (@$_GET['lang'])
            $_SESSION['translate_lang'] = @$_GET['lang'];
        if (!@$_SESSION['translate_lang']) {
            $_SESSION['translate_lang'] = 'en';
        }
        return $_SESSION['translate_lang'];
    }

    /**
     * Формирование массивов данных
     *
     * @param array $data
     */
    private function _getReferrals($data)
    {
        foreach ($data as $row) {
            /** дерево рефералов **/
            $this->referrals[$row['parent_id']][] = $row;
            /** массив всех рефералов **/
            $this->refdata[$row['id']] = $row;
        }
        /** выбираем рефералов для которых нужно вывести информацию **/
        $this->buildTree($this->pid);
    }

    /**
     * Создание цепочки родитетей
     * @return array
     */
    private function _getChain()
    {
        $chain = array();
        $parent_id = $this->pid;
        if (empty($parent_id))
            return $chain;
        do {
            if (isset($this->refdata[$parent_id])) {
                $reffer = $this->refdata[$parent_id];
                $parent_id = $reffer['parent_id'];
                $chain[$reffer['id']] = $reffer['login'];
            } else $parent_id = 0;
        } while ($parent_id != 0);
        return array_reverse($chain, TRUE);
    }

    /**
     * выбор id тех участников по которым будет выводиться информация
     * @param integer $pid
     * @return null
     */
    private function buildTree($pid)
    {
        if (isset($this->referrals[$pid])) {
            foreach ($this->referrals[$pid] as $item) {
                // если рефералл является участником системы то
                if ($item['in_system'])
                    $this->showdata[] = $item['id'];
                $this->buildTree($item['id']);
            }
        } else return null;
    }

    /**
     * @param RequestWrapper $request
     */
    public function sendPresentAction($request)
    {
        global $otapilib;
        $cms = new CMS();
        $status = $cms->Check();

        $id = intval($request->get('id'));
        try {
            $orderId = $this->getOrderForPresent($id);
            $result = $this->updateOrderForPresent($orderId);

            $cms->sendReferralMessage('Вам подарок',
                'Поздравляем!</br> Один из привлеченных Вами друзей оформил заказ! Выберите подарок!',
                'out', 0, $this->getParentUserForOrder($orderId));
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        if ($result) echo 'Ok';
        die();
    }
}

