<?php
OTBase::import('system.lib.service.OrderItem.Picture');

class OrderDetails extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'order'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/privateoffice/';

    protected $request;
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

    }

    protected function setVars()
    {
        $this->otapilib->setErrorsAsExceptionsOn();
        if(!Session::getUserData()){
            Users::Logout();
            header('Location: /?p=login');
            return ;

        }
        $this->sid = Session::getUserSession();
        if(!$this->request->valueExists('orderid'))
        {
            header('Location:/?p=privateoffice');
        }
        $order = $this->request->getValue('orderid');



        if (CFG_MULTI_CURL)
        {
            // С мультипотоками
            // Инициализируем
            $this->otapilib->InitMulti();
            $order_info = $this->otapilib->GetSalesOrderDetails($this->sid, $order);
            if (isset($GLOBALS['$otapilib->GetUserInfo']))
            { } else {
                $userinfo = $this->otapilib->GetUserInfo($this->sid);
            }
            $accountinfo = $this->otapilib->GetAccountInfo($this->sid);
            $shippinginfo = $this->otapilib->GetSalesOrderShippings($this->sid, $order);
            // Делаем запросы
            $this->otapilib->MultiDo();
            try{
                $order_info = $this->otapilib->GetSalesOrderDetails($this->sid, $order);

                if (isset($GLOBALS['$otapilib->GetUserInfo']))
                {
                    $userinfo = $GLOBALS['$otapilib->GetUserInfo'];
                } else {
                    $userinfo = $this->otapilib->GetUserInfo($this->sid);
                }
                $accountinfo = $this->otapilib->GetAccountInfo($this->sid);
                $shippinginfo = $this->otapilib->GetSalesOrderShippings($this->sid, $order);

            }
            catch(ServiceException $e){
                Session::setError($e->getMessage(), $e->getErrorCode());
            }
            // Сбрасываем
            $this->otapilib->StopMulti();
        } else {
            // По старому
            try{
                $order_info = $this->otapilib->GetSalesOrderDetails($this->sid, $order);
                //echo $otapilib->error_message;
                if (isset($GLOBALS['$otapilib->GetUserInfo']))
                {
                    $userinfo = $GLOBALS['$otapilib->GetUserInfo'];
                } else {
                    $userinfo = $this->otapilib->GetUserInfo($this->sid);
                }
                $accountinfo = $this->otapilib->GetAccountInfo($this->sid);
                $shippinginfo = $this->otapilib->GetSalesOrderShippings($this->sid, $order);
            }
            catch(ServiceException $e){
                Session::setError($e->getMessage(), $e->getErrorCode());
            }

        }

        $GLOBALS['amount'] = (string)$order_info['salesorderinfo']['remainamount'];
        $Pay = new Pay();
        $P = $Pay->Generate();
        $this->tpl->assign('Pay', $P);



        $StatusList = array(
            Lang::get('ordered_goods'),
            Lang::get('goods_in_handling'),
            Lang::get('cancelled_goods'),
            Lang::get('goods_with_questions')
        );
        $order_info['saleslineslist'] = $this->_filterGoods($order_info['saleslineslist'],$StatusList);

        if (! empty($order_info['saleslineslist']) && is_array(($order_info['saleslineslist']))) {
            $order_info['saleslineslist'] = $this->getPristroyItems($order_info['saleslineslist']);
        }

        $this->tpl->assign('StatusList', $StatusList);
        $this->tpl->assign('accountinfo', $accountinfo);
        $this->tpl->assign('shippinginfo', $shippinginfo);
        $this->tpl->assign('orderid', $order);
        if (isset($order_info['salesorderinfo']['custcomment']) && $order_info['salesorderinfo']['custcomment']) {
            $this->tpl->assign('orderComment', OrdersProxy::prepareOrderComment($order_info['salesorderinfo']['custcomment']));
        } else {
            $this->tpl->assign('orderComment', '');
        }

        if (in_array('PhotoReport', General::$enabledFeatures))  {
            $order_info = $this->_PhotoReport($order_info);
        }
        $this->tpl->assign('order_info', $order_info);
    }
    
    private function getPristroyItems(array $goods)
    {
        $ids = array();
        foreach ($goods as $item) {
            $ids[$item['id']] = $item['id'];
        }
        $pristroy = new PristroyRepository($this->cms);
        $pristroyItems = $pristroy->getListByItemIds($ids);
        foreach ($goods as &$item) {
           if (! empty($pristroyItems[$item['id']])) {
               $item['pristroy'] = $pristroyItems[$item['id']];
           }
        }
        return $goods;
    }


    private function _processOperatorComment ($comment, $urls) {
        foreach ($urls as $url) {
            if (strpos($comment, $url) !== false) {
                $img_name = Lang::get('image_link');
                $comment = str_replace($url, "<br/><a href='{$url}' target='_blank'>{$img_name}</a>", $comment);
            }
        }
        return $comment;
    }

    private function _filterGoods($goodsList,$filterTerms) {
        if (!isset($_GET['filter']['state'])||empty($_GET['filter']['state']))
            return $goodsList;
        $result = array();
        foreach ($goodsList as $good){
            if ($_GET['filter']['state']==$filterTerms[0]){
                if ($good['StatusId']==4)
                    $result[] = $good;
            }
            elseif ($_GET['filter']['state']==$filterTerms[1]){
                if ($good['StatusId']==2)
                    $result[] = $good;
            }
            elseif ($_GET['filter']['state']==$filterTerms[2]){
                if ($good['StatusId']==13)
                    $result[] = $good;
            }
            elseif ($_GET['filter']['state']==$filterTerms[3]){
                if ($good['StatusId']==3||$good['StatusId']==5||$good['StatusId']==12)
                    $result[] = $good;
            }
        }
        return $result;
    }




    private function _PhotoReport($order_info)
    {
        $orderId = $order_info['SalesOrderInfo']['Id'];
        foreach($order_info['saleslineslist'] as &$item) {
            $oldWebcamFiles = glob(CFG_APP_ROOT . '/files/ItemCam/' . $orderId . '-' . $item['id'] . '*.jpg');
            $oldWebcamFiles = is_array($oldWebcamFiles) ? $oldWebcamFiles : array();

            $uploadedFiles = glob(CFG_APP_ROOT . '/uploaded/items/' . $item['id'] . '/' . $orderId . '/*.*');
            $uploadedFiles = is_array($uploadedFiles) ? $uploadedFiles : array();

            $operatorFiles = array();
            $item['operatorcomment'] = str_replace('\n', "\n", $item['operatorcomment']);
            $item['OperatorComment'] = str_replace('\n', "\n", $item['OperatorComment']);
            preg_match_all('#https?:\/\/\S+\/(.+)(jpg | jpeg | png | ico | gif | bmp)#si', $item['operatorcomment'], $m);
            preg_match_all('#https?:\/\/\S+\/(.+)(jpg | jpeg | png | ico | gif | bmp)#si', $item['OperatorComment'], $m);
            if (! empty($m[0])) {
                foreach ($m[0] as &$url) {
                    $url = str_replace(' ', '%20', $url);
                }
                $operatorFiles = $m[0];
            }

            $pictures = array_merge($oldWebcamFiles, $uploadedFiles, $operatorFiles);
            $picturesFull = array();
            foreach ($pictures as $file) {
                $picturesFull[] = new Picture($file);
                $item['operatorcomment'] = str_replace($file, '', $item['operatorcomment']);
            }
            $item['operatorimages'] = $picturesFull;
            $item['OperatorImages'] = $picturesFull;
        }
        return $order_info;
    }

}

?>