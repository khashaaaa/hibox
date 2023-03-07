<?php

class ProviderConfiguration extends GeneralUtil
{
    protected $_template = 'provider_configuration';
    protected $_template_path = 'provider_config/';

    /**
     * @param RequestWrapper $request
     */
    public function defaultAction($request)
    {
        $lang = Session::getActiveAdminLang();
        $providers = array();
        $providerSettings = array();

        try {
            $instanceProvider = InstanceProvider::getObject();
            $providersList = $instanceProvider->getAvailableProviders($lang);

            foreach ($providersList as $item) {
                if ($item->IsEnabled() && $item->HasSettings()) {
                    $providers[] = $item;
                    $settings = array();

                    OTAPILib2::GetProviderSettings($lang, Session::get('sid'), $item->GetType(), 'true', $settings);
                    OTAPILib2::makeRequests();

                    $providerSettings[(string)$item->GetType()] = $settings->GetResult()->GetRawData();
                }
            }
        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);
        }

        $this->tpl->assign('providers', $providers);
        $this->tpl->assign('PageUrl', $this->pageUrl);
        $this->tpl->assign('providerSettings', $providerSettings);
        $this->tpl->assign('updateSettingsUrl', '?cmd=ProviderConfiguration&do=update');

        print $this->fetchTemplate();
    }

    public function getProviderMetaUIAction($request)
    {
        $lang = Session::getActiveAdminLang();
        $type = $request->getValue('type');
        $providerSettings = false;

        try {
            $instanceProvider = InstanceProvider::getObject();
            $providersList = $instanceProvider->getAvailableProviders($lang);

            $settings = array();
            OTAPILib2::GetProviderSettings($lang, Session::get('sid'), $type, 'true', $settings);
            OTAPILib2::makeRequests();

            $providerSettings = $settings->GetResult()->GetRawData();

            $html = General::viewFetch('provider_config/metaUI', array(
                'path' => TPL_PATH,
                'vars' => array(
                    'type' => $type,
                    'providerSettings' => $providerSettings,
                    'updateSettingsUrl' => '?cmd=ProviderConfiguration&do=update'
            )));
        } catch (ServiceException $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse(array('html' => $html));
    }

    public function updateAction($request)
    {
        $name = $request->post('name');
        $value = $request->post('value');
        $type = $request->get('type');

        try {
            $params = explode(MetaUI::NODES_SEPARATOR, $name);
            if (is_array($params) && count($params) > 0) {
                $providerType = $request->get('providerType');
                $xmlParameters = MetaUI::generateSingleParamXml('ProviderSettingsUpdateData', $params, $value, $type);
                $answer = false;
                OTAPILib2::UpdateProviderSettings(Session::getActiveAdminLang(), Session::get('sid'), $providerType, $xmlParameters, $answer);
                OTAPILib2::makeRequests();

            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array(), true);
    }

    /**
     * @param RequestWrapper $request
     */
    public function searchTypeAction($request)
    {
        $this->_template_path = 'provider_config/search_type/';
        $this->_template = 'general';

        $lang = Session::getActiveAdminLang();
        $searchTypes = null;
        $searchTypesMetaInfo = null;

        try {
            OTAPILib2::GetProviderSearchMethodSettings($lang, Session::get('sid'), 'true', $searchTypes);
            OTAPILib2::makeRequests();

            if ($searchTypes && $searchTypes->GetResult()) {
                $searchTypesMetaInfo = $searchTypes->GetResult()->GetRawData();
            }
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }

        $this->tpl->assign('searchTypesMetaInfo', $searchTypesMetaInfo);
        $this->tpl->assign('updateSettingsUrl', 'index.php?cmd=ProviderConfiguration&do=updateSearchType');
        $this->tpl->assign('searchSettings', $this->getHtmlForSearchSettings());

        print $this->fetchTemplate();
    }

    public function updateSearchTypeAction($request)
    {
        $name = $request->post('name');
        $value = $request->post('value');
        $type = $request->get('type');

        try {
            $params = explode(MetaUI::NODES_SEPARATOR, $name);
            if (is_array($params) && count($params) > 0) {
                $xmlParameters = MetaUI::generateSingleParamXml('ProviderSearchMethodSettingsUpdateData', $params, $value, $type);
                $answer = false;
                OTAPILib2::UpdateProviderSearchMethodSettings(Session::getActiveAdminLang(), Session::get('sid'), $xmlParameters, $answer);
                OTAPILib2::makeRequests();
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array(), true);
    }
    
    private function getHtmlForSearchSettings()
    {
        $methodsAnswer = array();
    
        try {
            $lang = Session::get('active_lang_providerconfiguration') ? Session::get('active_lang_providerconfiguration') : Session::getActiveAdminLang();
            $langs = $this->languagesProvider->GetActiveLanguages();
            if (! isset($langs[$lang])) {
            	$lang = isset($langs[Session::getActiveAdminLang()]) ? Session::getActiveAdminLang() : array_shift(array_keys($langs));
            	Session::setActiveAdminLang($lang);
            }
            Session::set('active_lang_providerconfiguration', $lang);
            
            
            OTAPILib2::GetProviderSearchMethods(Session::getActiveAdminLang(), Session::get('sid'), $lang, $methodsAnswer);
            OTAPILib2::makeRequests();
            if ($methodsAnswer && $methodsAnswer->GetResult()->GetContent()->GetItem()) {
                $methodsAnswer = $methodsAnswer->GetResult()->GetContent()->GetItem();
            }
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }
    
        return General::viewFetch('provider_config/search_settings', array(
            'path' => TPL_PATH,
            'vars' => array(
                'searchMethods' => $methodsAnswer,
                'PageUrl' => $this->pageUrl
            ),
        ));
    }
    
    public function getSearchMethodInfoAction($request)
    {
        $html = '';
    
        try {
            $id = $request->getValue('id');
            $langs = $this->languagesProvider->GetActiveLanguages();
            $lang = Session::get('active_lang_providerconfiguration') ? Session::get('active_lang_providerconfiguration') : Session::getActiveAdminLang();
            if (! isset($langs[$lang])) {
            	$lang = isset($langs[Session::getActiveAdminLang()]) ? Session::getActiveAdminLang() : array_shift(array_keys($langs));
            	Session::setActiveAdminLang($lang);
            }
           	Session::set('active_lang_providerconfiguration', $lang);
            
            
            OTAPILib2::GetProviderSearchMethod(Session::getActiveAdminLang(), Session::get('sid'), $lang, $id, 'true', $settings);
            OTAPILib2::makeRequests();
            
            if ($settings && $settings->GetResult()) {
                $setting = $settings->GetResult()->GetRawData();
                $html = General::viewFetch('provider_config/search_method_info', array(
                    'path' => TPL_PATH,
                    'vars' => array(
                        'methodId' => $id,
                        'setting' => $setting,
                        'updateSettingsUrl' => $this->pageUrl->Add('do', 'updateSearchMethodInfo')->Get() . '&methodId=' . $id,
                    ),
                ));
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
    
        $this->sendAjaxResponse(array('html' => $html));
    }

    public function updateSearchMethodInfoAction($request)
    {
        $name = $request->post('name');
        $value = $request->post('value');
        $type = $request->get('type');
        $methodId = $request->get('methodId');
        
        try {
        	$langs = $this->languagesProvider->GetActiveLanguages();
            $lang = Session::get('active_lang_providerconfiguration') ? Session::get('active_lang_providerconfiguration') : Session::getActiveAdminLang();
        	if (! isset($langs[$lang])) {
        		$lang = isset($langs[Session::getActiveAdminLang()]) ? Session::getActiveAdminLang() : array_shift(array_keys($langs));  
        		Session::setActiveAdminLang($lang);
        	}
        	Session::set('active_lang_providerconfiguration', $lang);
            $xmlUpdateData = MetaUI::generateSingleParamXml('ProviderSearchMethodUpdateData', array($name), $value, $type);
            $answer = false;
                
            OTAPILib2::UpdateProviderSearchMethod(Session::getActiveAdminLang(), Session::get('sid'), $lang, $methodId, $xmlUpdateData, $answer);
            OTAPILib2::makeRequests();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array(), true);
        
    } 
    
}
