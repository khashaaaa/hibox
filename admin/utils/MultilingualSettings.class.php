<?php

class MultilingualSettings extends GeneralUtil
{
    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = 'multi';
    protected $_template_path = 'lang/';
    protected $cacher;

    public function __construct()
    {
        parent::__construct();
        $this->cms->checkTable('site_langs');
        $this->cacher = new FileAndMysqlMemoryCache($this->cms);
    }

    public function defaultAction($request)
    {
        try {
        	$WebUI = $this->languagesProvider->GetLanguages();
        	if ($WebUI->Languages) { 
	        	foreach($WebUI->Languages->NamedProperty as $langSearch) {
	        		$this->cms->checkLanguage((string)$langSearch->Name, (string)$langSearch->Description);
	        	}
        	}
            $this->tpl->assign('WebUI', $WebUI);
        } catch(ServiceException $e) {
            $this->errorHandler->CheckSessionExpired($e, $request);

            $this->tpl->assign('ServiceError', $e);
            $this->_template = 'error';
            $this->_template_path = '/';
        }
        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function addLangToShowcaseAction($request)
    {
        $this->languagesProvider->AddLanguageToWebUI($request->getValue('new_language'));
        InstanceProvider::getObject()->clearCommonInstanceOptionsInfoCache();
        $request->RedirectToReferrer();
    }

    /**
     * @param RequestWrapper $request
     */
    public function deleteLangFromShowcaseAction($request)
    {
        $this->languagesProvider->DeleteLanguageFromWebUI($request->getValue('delete_language'));
        InstanceProvider::getObject()->clearCommonInstanceOptionsInfoCache();
        $request->RedirectToReferrer();
    }

    /**
     * @param RequestWrapper $request
     */
    public function saveLangOrderAction ($request)
    {
        try {
            $this->languagesProvider->SaveLanguagesOrder($request->getValue('langs'));
            InstanceProvider::getObject()->clearCommonInstanceOptionsInfoCache();
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }
}
