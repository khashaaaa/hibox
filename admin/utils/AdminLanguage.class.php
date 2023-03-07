<?php
class AdminLanguage extends GeneralUtil
{
    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = 'lang_js';
    protected $_template_path = 'translation/';

    /**
     * @param RequestWrapper $request
     */
    public function setSiteLangAction($request)
    {
        Session::set('active_lang', $request->getValue('lang'));
        $retpath = $request->getValue('retpath');
        $retpath = !empty($retpath) ? $retpath : RequestWrapper::env('HTTP_REFERER', '/admin/');
        $this->redirect($retpath);
    }

    /**
     * @param RequestWrapper $request
     */
    public function setAdminLangAction($request)
    {
        Session::set('active_lang_admin', $request->getValue('lang'));
        $retpath = $request->getValue('retpath');
        $retpath = !empty($retpath) ? $retpath : RequestWrapper::env('HTTP_REFERER', '/admin/');
        $this->redirect($retpath);
    }

    public function setStatusAdminLangAction($request)
    {
        Session::set('active_status_lang_admin', $request->getValue('lang'));
        $retpath = $request->getValue('retpath');
        $retpath = !empty($retpath) ? $retpath : RequestWrapper::env('HTTP_REFERER', '/admin/');
        $this->redirect($retpath);
    }

    /**
     * @param RequestWrapper $request
     */
    public function setPageLangAction($request)
    {
        $referer = new UrlWrapper();
        $cmd = $referer->Set($_SERVER['HTTP_REFERER'])->GetKey('cmd');
        if (! $cmd) {
            $cmd = Permission::default_cmd();
        }
        Session::set('active_lang_' . strtolower($cmd), $request->getValue('lang'));

        $retpath = $request->getValue('retpath');
        $retpath = !empty($retpath) ? $retpath : RequestWrapper::env('HTTP_REFERER', '/admin/');
        $this->redirect($retpath);
    }

    public function getTranslationsAction($request)
    {
        header('Content-type: text/javascript');
        $M = new FileAndMysqlMemoryCache(new CMS());
        $lang = Session::getActiveAdminLang();
        $section = $request->getValue('section');
        $version = OTBase::isTest() ? null : $request->getValue('ver');
        $this->tpl->assign('currentAdminLang', $lang);
        $cacheKey = implode(':', array_filter(array('admin', 'translations', $lang, $section, $version)));
        if (! OTBase::isTest() && $M->Exists($cacheKey)) {
            $this->tpl->assign('translations', $M->GetCacheEl($cacheKey));
        }
        else {
            $translations = json_encode(LangAdmin::getAllTranslations());
            $M->AddCacheEl($cacheKey, 3600, $translations);
            $this->tpl->assign('translations', $translations);
        }
        print $this->fetchTemplateWithoutHeaderAndFooter();
    }

    public function getActiveLanguages($availableLanguages = null, $showAll = true)
    {
        $this->_template_path = 'lang/';
        $this->_template = 'active';
        try {
            if (is_null($availableLanguages)) {
                $langsObject = InstanceProvider::getObject()->GetLanguageInfoList();
                $availableLanguages = $this->otapilib->GetLanguageInfoList($langsObject->asXML());
            }
            $this->tpl->assign('languages', $availableLanguages);
            $this->tpl->assign('showAll', $showAll);
            
            return $this->fetchTemplateWithoutHeaderAndFooter();
        }
        catch (ServiceException $e) {
            return $this->errorHandler->showErrorWithPNotify($e);
        }
    }
    
    public static function getLanguageSelector($showAll = true) 
    {
        global $otapilib;
        $languagesProvider = new LanguageSettings($otapilib);
        $languages = $languagesProvider->GetActiveLanguages();

        // set default
        $cmd = RequestWrapper::get('cmd');
        if ($cmd && !empty($languages) && !Session::get('active_lang_' . strtolower($cmd))) {
            foreach ($languages as $key => $value) {
                Session::set('active_lang_' . strtolower($cmd), $key);
                break;
            }
        }

        $pageUrl = new AdminUrlWrapper();
        $pageUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        
        return General::viewFetch('lang/active', array(
            'path' => TPL_PATH,
            'vars' => array(
                'languages' => $languages,
                'showAll' => $showAll,
                'PageUrl' => $pageUrl
            ),
        ));
    }
}