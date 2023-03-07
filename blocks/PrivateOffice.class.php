<?php
OTBase::import('system.lib.referral_system.ReferalSystem');
OTBase::import('system.lib.referral_system.lib.*');

class PrivateOffice extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'maininfo'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/privateoffice/';

    private $currencyData = array();

    protected $request;
    protected $pristroy;
    protected $sid;

    // статусы заказа в ЛК пользователя
    const ORDER_WAITING             = 10;  // ожидает оплаты
    const ORDER_UNDER_CONSIDERATION = 20;  // на рассмотрении
    const ORDER_PROCESS             = 30;  // в обработке
    const ORDER_COMPLITE            = 40;  // завершено
    const ORDER_CANCEL              = 50;  // отменено

    // статусы товара
    const PRODUCT_IMPOSSIBLE_TO_PUT = 12;  // невозможно поставить
    const PRODUCT_UNDER_CONSIDERATION = 20;  // на рассмотрении

    public function __construct()
    {
        parent::__construct(true);
        $this->request = new RequestWrapper();
        $this->pristroy = new PristroyRepository(new CMS());

    }

    protected function setVars()
    {
        $currency_list = InstanceProvider::getObject()->getCurrencyInstanceList();
        $this->currencyData = $this->otapilib->GetCurrencyInstanceList($currency_list->asXML());
        if ($this->currencyData) {
            $this->currencyData = $this->currencyData['Internal'];
        }

        $this->otapilib->setErrorsAsExceptionsOn();
        if(! Session::isAuthenticated()){
            Users::Logout();
            header('Location: /?p=login');
            return ;
        }
        $this->sid = Session::getUserSession();
        if (CFG_MULTI_CURL)
        {
            // С мультипотоками
            // Инициализируем
            $this->otapilib->InitMulti();
            $orders = $this->otapilib->GetSalesOrdersList($this->sid, 0);
            $accountinfo = $this->otapilib->GetAccountInfo($this->sid);
            if (isset($GLOBALS['$otapilib->GetUserInfo']))
            {
                $userinfo = $GLOBALS['$otapilib->GetUserInfo'];
            } else {
                $userinfo = $this->otapilib->GetUserInfo($this->sid);
            }

            // Делаем запросы
            $this->otapilib->MultiDo();
            try{
                $orders = $this->otapilib->GetSalesOrdersList($this->sid, 0);
                $accountinfo = $this->otapilib->GetAccountInfo($this->sid);
                //var_dump($accountinfo);
                if (isset($GLOBALS['$otapilib->GetUserInfo']))
                {
                    $userinfo = $GLOBALS['$otapilib->GetUserInfo'];
                } else {
                    $userinfo = $this->otapilib->GetUserInfo($this->sid);
                    $GLOBALS['$otapilib->GetUserInfo'] = $userinfo;
                }
            }
            catch(ServiceException $e){
                Session::setError($e->getMessage());
            }
            // Сбрасываем
            $this->otapilib->StopMulti();
        } else {
            // По старому
            try{
                $orders = $this->otapilib->GetSalesOrdersList($this->sid, 0);
                $accountinfo = $this->otapilib->GetAccountInfo($this->sid);
                if (isset($GLOBALS['$otapilib->GetUserInfo']))
                {
                    $userinfo = $GLOBALS['$otapilib->GetUserInfo'];
                } else {
                    $userinfo = $this->otapilib->GetUserInfo($this->sid);
                    $GLOBALS['$otapilib->GetUserInfo'] = $userinfo;
                }
            }
            catch(ServiceException $e){
                Session::setError($e->getMessage());
            }
        }
        if($orders === false){
            show_error();
            $orders = array();
        }

        $orders  = array_reverse($orders);
        $orders_active = array();
        $orders_complited = array();
        $orders_canceled = array();
        $orders_waited = array();
        foreach ($orders as $order) {
            if ($order['statuscode'] == self::ORDER_COMPLITE) {
                $orders_complited[] = $order;
            } elseif($order['statuscode'] == self::ORDER_CANCEL) {
                $orders_canceled[] = $order;
            } elseif($order['statuscode'] == self::ORDER_WAITING) {
                $orders_waited[] = $order;
            } else {
                $orders_active[] = $order;
            }
        }



        $user = array();
        foreach($userinfo as $key=>$value){
            $key = strtolower((string)$key);
            if(!isset($user[$key]))  $user[$key] = (string)$value;
        }
        $Discount = new Discount();
        $userDiscounts = $Discount->getDiscountsData();
        // Товары пристроя.
        if (CMS::IsFeatureEnabled('FleaMarket')) {
            $this->getUserSellItems($userinfo{'id'});
        }
        //===========================
        //Гугл коммерция
        if (General::getConfigValue('google_commerce_account'))
            $this->setGoogleCommerce($orders);
        //===============
        unset($user['isemailverified']);
        unset($user['isactive']);
        unset($user['password']);
        $this->tpl->assign('orders', $orders);
        $this->tpl->assign('orders_active', $orders_active);
        $this->tpl->assign('orders_complited', $orders_complited);
        $this->tpl->assign('orders_canceled', $orders_canceled);
        $this->tpl->assign('orders_waited', $orders_waited);
        $this->tpl->assign('pay_info', false);
        $this->tpl->assign('userDiscounts', $userDiscounts);
        $this->tpl->assign('userinfo', $user);
        $this->tpl->assign('accountinfo', $accountinfo);

        if($this->request->valueExists('message')){
            $this->tpl->assign('message',$this->request->getValue('message') );
        }
        if($this->request->valueExists('error')){
            $this->tpl->assign('error',$this->request->getValue('error'));
        }
        if($this->request->valueExists('success')){
            $this->tpl->assign('success',$this->request->getValue('success'));
        }
        $this->_getPayModes();
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
        $this->tpl->assign('currency', $this->currencyData ? $this->currencyData : array());
        $this->tpl->assign('currencySign', $this->currencyData ? $this->currencyData['Sign'] : '');
        $this->tpl->assign('enter_money', 1);

        return true;
    }



    public function PAPayAction () {
        $this->otapilib->setErrorsAsExceptionsOn();
        if(! Session::isAuthenticated()) {
            Users::Logout();
            header('Location: /?p=login');
            return ;
        }
        try {            
            $this->otapilib->PaymentPersonalAccount(Session::getUserSession(), $this->request->getValue('salesId'), $this->request->getValue('amount'));

            $userData = new UserData();
            $userData->ClearAccountInfoCache();
        } catch(ServiceException $e) {
            if (strpos((string)$e->getMessage(), 'Not enough') !== false) {
                Session::setError(Lang::get('Not_enoght_money_on_account') );
            } else {
                Session::setError($e->getMessage(), $e->getErrorCode());
            }
        }
        header('Location: /?p=orderdetails&orderid=' . $this->request->getValue('salesId'));
    }

    public function CancelOrderAction () {
            $this->otapilib->setErrorsAsExceptionsOn();
            if(! Session::isAuthenticated()){
                Users::Logout();
                header('Location: /?p=login');
                return ;
            }
            $this->sid = Session::getUserSession();
            try{
                $res = $this->otapilib->CancelSalesOrder($this->sid, $this->request->getValue('order_id'));
            }
            catch(ServiceException $e){
                Session::setError($e->getMessage());
            }
            header('Location:/?p=privateoffice');
    }

    public function ConfirmShipmentAction ()
    {
        $this->otapilib->setErrorsAsExceptionsOn();
        if(! Session::isAuthenticated())
        {
            Users::Logout();
            header('Location: /?p=login');
            return ;
        }
        $this->sid = Session::getUserSession();
        try{
            $res = $this->otapilib->ConfirmOrderPackaging($this->sid, $this->request->getValue('order_id'));
        }
        catch(ServiceException $e){
            Session::setError($e->getMessage());
        }
        header('Location:/?p=privateoffice');
    }

    public function CloselOrderAction () {
            $this->otapilib->setErrorsAsExceptionsOn();
            if(! Session::isAuthenticated()){
                Users::Logout();
                header('Location: /?p=login');
                return ;
            }
            $this->sid = Session::getUserSession();
            try{
                $res = $this->otapilib->CloseOrder($this->sid, $this->request->getValue('order_id'));
            }
            catch(ServiceException $e){
                Session::setError($e->getMessage());
            }
            header('Location:/?p=privateoffice');
    }

    /**
     * @param RequestWrapper $request
     */
    public function confirmNewItemPriceInOrderAction($request){
        $this->otapilib->setErrorsAsExceptionsOn();
        $itemId = RequestWrapper::get('itemid');
        $orderId = RequestWrapper::get('orderid');
        $sid = Session::getUserDataSid();
        try{
            $this->otapilib->ConfirmPriceLineSalesOrder($sid, $orderId, $itemId);
        } catch (ServiceException $e) {
            $this->throwAjaxError($e);
        }
    }

    private function getUserSellItems($userId)
    {
        $user_items = $this->pristroy->getListByUserId($userId,null,'All');
        $SellingItems = array();
        foreach($user_items as $item){
            $tmp['Id'] = $item['item_id'];
            $tmp['id'] = $tmp['Id'];
            $tmp['Qty'] = $item['quantity'];
            $tmp['qty'] = $tmp['Qty'];
            if (is_array($item['images']) && count($item['images']) > 0) {
                $tmp['ItemImageURL'] = array_shift($item['images']);
                $tmp['itemimageurl'] = $tmp['ItemImageURL'];
            }
            $item['created_at'] = strtotime($item['created_at']);
            $item['created_at'] = date('Y-m-d H:i:s', $item['created_at']);
            $tmp['pristroy'] = $item;
            $SellingItems[]=$tmp;
        }
        $this->tpl->assign('SellingItems', $SellingItems);
    }

    private function setGoogleCommerce($orders)
    {
        $orders_payed = array();
        $orders_transfer = array();
        $orders_transfer_js = array();
        foreach ($orders as $order) {
            if($order['statuscode'] == self::ORDER_UNDER_CONSIDERATION)
                $orders_payed[] = $order;
        }

        $GCR = new GoogleCommerceRepository(new CMS());
        $tmp = array();
        foreach($orders_payed as $order){
            if (!$GCR->CheckOrder($order['id'],(float)$order['totalamount'])) {
                $tmp[] = $order;
                $tmp_js['id'] = $order['id'];
                $tmp_js['amount'] = (float)$order['totalamount'];
                $orders_transfer_js[] = $tmp_js;
            }
        }

        if (CFG_MULTI_CURL) {
            $this->otapilib->InitMulti();
            foreach($tmp as $order){
                $order_info = $this->otapilib->GetSalesOrderDetails($this->sid, $order['id']);
            }
            $this->otapilib->MultiDo();
        }
        foreach($tmp as $order){
            $order_info = $this->otapilib->GetSalesOrderDetails($this->sid, $order['id']);
            $orders_transfer[] = $order_info;
        }
        if (CFG_MULTI_CURL)
            $this->otapilib->StopMulti();

        $this->tpl->assign('orders_transfer', $orders_transfer);
        $this->tpl->assign('orders_transfer_js', $orders_transfer_js);

    }
    
    public function setTransferedOrdersAction($request)
    {       
        $GCR = new GoogleCommerceRepository(new CMS());
        try {
            $GCR->checkAndSaveOrders($this->request->post('orders'));
        } catch (DBException $e) {
            Session::setError($e->getMessage(), 'DBError_GoogleCommerce');                
        }            
    }
    
}
