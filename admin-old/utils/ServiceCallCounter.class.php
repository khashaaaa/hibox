<?php

/**
 * Shows count of service calls
 */
class ServiceCallCounter extends GeneralUtil
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'index'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = 'service_calls/'; //- путь к шаблону
    protected $tpl;

    public function defaultAction(){
        $this->checkAuth();
        print $this->fetchTemplate();
    }

    public function getCountAction(){
        global $otapilib;

        $S = new ServiceCalls($otapilib);
        print json_encode($S->GetCallStatisticFromOTAPI());

        die();
    }
	public function getTarifAction(){
        global $otapilib;
        try{
            $otapilib->setErrorsAsExceptionsOn();
            $sid = Session::get('sid');
            $optionsInfo = $otapilib->GetInstanceOptionsInfo($sid);
            print json_encode($optionsInfo);
        }
        catch(ServiceException $e){
            $this->throwAjaxError($e);
        }

        die();
    }
}
