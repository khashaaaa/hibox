<?php
class Order_settings extends GeneralUtil {
    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = 'index';
    protected $_template_path = 'order_settings/';

    public function __construct()
    {
        parent::__construct();

        global $otapilib;
        $otapilib->setErrorsAsExceptionsOn();
    }

    public function defaultAction()
    {
        global $otapilib;
        $order_settings = $otapilib->GetOrderSettingsInfo(Session::get('sid'));
        $this->tpl->assign('order_settings', $order_settings);
        print $this->fetchTemplate();
        Session::checkAdminErrors();
    }

    /**
     * Сохранить настройки
     * @param RequestWRapper $request
     * @return bool
     */
    public function saveSettingsAction($request)
    {
        global $otapilib;
        try{
            $otapilib->UpdateOrderSettings(Session::get('sid'), $this->generateUpdateXML($request));
        }
        catch(ServiceException $e){
            if($e->getErrorCode() == 'SessionExpired')
                return $this->setErrorAndRedirect('', 'index.php?cmd=login');
            Session::setError($e->getMessage(), $e->getErrorCode());
        }
        header('Location: index.php?cmd=order_settings');
    }

    /**
     * @param RequestWRapper $request
     * @return string XML
     */
    private function generateUpdateXML($request){
        $updateXML = new SimpleXMLElement('<OrderSettingsUpdateData></OrderSettingsUpdateData>');
        $updateXML->addChild('MinOrderCost', $request->post('MinOrderCost'));
        $updateXML->addChild('MaxNoteItemsCount', $request->post('MaxNoteItemsCount'));
        $updateXML->addChild('MaxCartItemsCount', $request->post('MaxCartItemsCount'));
        return $updateXML->asXML();
    }
}
