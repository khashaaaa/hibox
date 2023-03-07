<?php
	
class ActivitiesUtil extends GeneralUtil
{
    protected $_template = 'list';
    protected $_template_path = 'activities/';
    

    public function __construct()
	{
        parent::__construct();
    }

    /*
     * Get Activities list
     * $types - array. Can be {ProviderOrdersIntegrationExporting, Test}
     * */
    public function getActivitiesListView($types)
    {
        $result = array();
        try{
            $language =  Session::getActiveAdminLang();
            $sessionId = Session::get('sid');
        
            $xmlParameters = '';
            $xmlParams = new SimpleXMLElement('<BackgroundActivitySearchParameters></BackgroundActivitySearchParameters>');
            $orderLineIdsXml = $xmlParams->addChild('Types');
            foreach ($types as $key => $type) {
                $orderLineIdsXml->addChild('Type', $type);
            }
        
            $xmlParameters = str_replace('<?xml version="1.0"?>','',$xmlParams->asXML());
        
            $answer = array();
            OTAPILib2::SearchBackgroundActivities($language, $sessionId, $xmlParameters, $answer);
            OTAPILib2::makeRequests();
        
            $result = array();
            if ($answer && $answer->GetResult()->GetContent()->GetItem()) {
                $result = $answer->GetResult()->GetContent()->GetItem()->toArray();
            }
        
        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);
        }
        
        $this->_template = 'list';
        $this->tpl->assign('result', $result);
        return  $this->fetchTemplateWithoutHeaderAndFooter(false);
    }
    
    public function getActivityInfoAction($request)
    {
        $activityId = $request->getValue('id');
        $activityType = $request->getValue('type');
        $answer = array();
        $content = '';
        $isFinished = false;
    
        try {
            $sessionId = Session::get('sid');
            $language =  Session::getActiveAdminLang();
            
            OTAPILib2::GetBackgroundActivityInfo($language, $sessionId, $activityType, $activityId, $answer);
            OTAPILib2::makeRequests();
            
            $this->_template = 'activity-info';
            $this->tpl->assign('activity', $answer->GetResult());
            $isFinished = $answer->GetResult()->IsFinished();
            $content =  $this->fetchTemplateWithoutHeaderAndFooter(false);
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array('content' => $content, 'result' => 'ok', 'isFinished' => $isFinished), true);
    }
    
    public function doActivityActionAction($request) 
    {
        $activityId = $request->getValue('activityId');
        $activityType = $request->getValue('activityType');
        $actionId = $request->getValue('actionId');
        
        try {
            $sessionId = Session::get('sid');
            $language =  Session::getActiveAdminLang();
            OTAPILib2::DoActionForBackgroundActivity($language, $sessionId, $activityType, $activityId, $actionId, $answer);
            OTAPILib2::makeRequests();
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array('result' => 'ok'), true);
    }

    public function doStepActivityAction($request) 
    {
        $activityId = $request->getValue('activityId');
        $activityType = $request->getValue('activityType');
        $params = $request->getValue('params');

        try {
            $sessionId = Session::get('sid');
            $language =  Session::getActiveAdminLang();

            $xmlParameters = '';
            $xmlParams = new SimpleXMLElement('<NamedParameters></NamedParameters>');
            foreach ($params as $key => $value) {
                $parameter = $xmlParams->addChild('Parameter', $value);
                $parameter->addAttribute('Name', $key);
            }
            $xmlParameters = str_replace('<?xml version="1.0"?>','',$xmlParams->asXML());

            $answer = new VoidOtapiAnswer(null);
            OTAPILib2::DoStepActionForBackgroundActivity($language, $sessionId, $activityType, $activityId, $xmlParameters, $answer);
            OTAPILib2::makeRequests();
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array('result' => 'ok'), true);
    }
}
