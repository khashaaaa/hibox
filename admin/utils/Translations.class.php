<?php

class Translations extends GeneralUtil {
    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = 'list';
    protected $_template_path = 'translation/';
    /**
     * @var TranslationsRepository
     */
    private $translationsRepository;

    public function __construct(){
        parent::__construct();
        $this->cms->checkTable('site_langs');
        $this->translationsRepository = new TranslationsRepository($this->cms);
    }

    /**
     * @param RequestWrapper $request
     */
    public function defaultAction($request)
    {
        $this->authenticationListener->CheckAuthentication($request);

        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function getTranslationsJSONAction($request){
        $this->authenticationListener->CheckAuthentication($request);
        $languages = $this->languagesProvider->GetActiveLanguages();
        $langKeys = array_keys($languages);
        $this->_template = 'list_json';
        $translations = $this->translationsRepository->GetAllTranslationsByKeys($languages);
        $this->tpl->assign('activeLang', $request->getValue('lang') ? $request->getValue('lang') : $langKeys[0]);
        $this->tpl->assign('sort', $request->getValue('sort'));
        $this->tpl->assign('translations', $translations);
        print $this->fetchTemplateWithoutHeaderAndFooter();
    }

    /**
     * @param RequestWrapper $request
     */
    public function deleteAction($request){
        $this->authenticationListener->CheckAuthentication($request);

        try{
            $this->translationsRepository->DeleteTranslationsByKeyFromDB($request->getValue('key'));
        }
        catch(Exception $e){
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    /**
     * @param RequestWrapper $request
     */
    public function editAction($request){
        $this->authenticationListener->CheckAuthentication($request);

        $this->_template = 'crud';
        $this->tpl->assign('isNew', false);
        $this->tpl->assign('key', $request->getValue('key'));
        $translations = $this->translationsRepository->GetTranslationsByKey($request->getValue('key'));
        $translationsByLangCodes = array();
        foreach($translations as $v){
            $translationsByLangCodes[$v['lang_code']] = $v['translation'];
        }
        $languages = $this->languagesProvider->GetActiveLanguages();
        $translations = $this->translationsRepository->GetAllTranslationsByKeys($languages);
        $translation = isset($translations[$request->getValue('key')]) ? $translations[$request->getValue('key')] : array();
        foreach ($translation as $lang => $value) {
            if (! array_key_exists($lang, $translationsByLangCodes) && is_array($value) && count($value)>0 ) {
                $translationsByLangCodes[$lang] = $value['translation'];
            }
        } 
        
        $this->tpl->assign('translation', $translationsByLangCodes);

        print $this->fetchTemplate();
    }
    
    /**
     * @param RequestWrapper $request
     */
    public function addAction($request){
        $this->authenticationListener->CheckAuthentication($request);
        $this->_template = 'crud';
        $this->tpl->assign('isNew', true);
        $this->tpl->assign('translation', array());
        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function saveKeyAction($request) {
        $escape = $request->getValue('escape') ? true : false;
        $translations = $request->post('translation');
        $keyTranslation = $request->post('key');

        foreach ($translations as $key => $translation) {
            $translations[$key] = !$escape ? htmlspecialchars($translation, ENT_QUOTES) : $translation;
        }

        $this->authenticationListener->CheckAuthentication($request);
        $this->translationsRepository->AddTranslation($keyTranslation, $translations);
        $request->LocationRedirect($this->pageUrl->DeleteKey('do')->Get());
    }

    /**
     * @param RequestWrapper $request
     */
    public function serviceAction($request)
    {
        $this->_template = 'service/list';
        $this->authenticationListener->CheckAuthentication($request);

        // paginator
        $page = $this->getPageDisplayParams($request);
        $perpage = $page['limit'];
        $pageNum = $page['number'];
        $from = $page['offset'];

        // language
        $languageSelect = AdminLanguage::getLanguageSelector(false); // init Session[active_lang_translations]
        $language = Session::getActiveAdminLang();

        $translatableList = array();
        $translations = array();
        $totalCount = 0;
        try {
            $translatableAnswer = null;
            $searchTranslations = null;

            if ($request->get('TranslatableContent')) {
                $xmlSearchParams = $this->generateSearchParameters($request);
                OTAPILib2::SearchTranslations($language, Session::get('sid'), $xmlSearchParams, $from, $perpage, $searchTranslations);
                OTAPILib2::makeRequests();
            }
            OTAPILib2::GetTranslatableContentList($language, Session::get('sid'), $translatableAnswer);
            OTAPILib2::makeRequests();
            if ($translatableAnswer && $translatableAnswer->GetResult()) {
                $translatableList = $translatableAnswer->GetResult()->GetContent();
            }
            if ($searchTranslations && $searchTranslations->GetResult()) {
                $translations = $searchTranslations->GetResult()->GetContent();
                $totalCount = $searchTranslations->GetResult()->GetTotalCount();
            }
        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }

        $this->tpl->assign('languageSelect', $languageSelect);
        $this->tpl->assign('translatableList', $translatableList);
        $this->tpl->assign('translations', $translations);
        $this->tpl->assign('totalCount', $totalCount);
        $this->tpl->assign('paginator', new Paginator($totalCount, $pageNum, $perpage));
        print $this->fetchTemplate();
    }

    public function searchAction($request) {
        $languagesDescription = array();
        $providersLanguagesAll = array();
        $searchTranslations = null;
        $this->_template = 'search/list';
        $this->authenticationListener->CheckAuthentication($request);
        $language = Session::getActiveAdminLang();
        $activePageLanguage = Session::get('active_lang_search_translations');
        $page = $this->getPageDisplayParams($request);
        $perpage = $page['limit'];
        $pageNum = $page['number'];
        $from = $page['offset'];
        $availableProviders = InstanceProvider::getObject()->getAvailableProviders($language);
        $request->set('TranslatableContent', "stuff:String:Value");

        foreach ($availableProviders as $key => $provider) {
            if ($activePageLanguage === null) {
                if ($key === 0) {
                    $activePageLanguage = $provider->GetLanguage();
                }
            }
            $providersLanguagesAll[] = $provider->GetLanguage();
        }

        Session::set('active_lang_search_translations', $activePageLanguage);

        $providersLanguages = array_unique($providersLanguagesAll);

        foreach ($providersLanguages as $item) {
            $providersName = array();
            foreach ($availableProviders as $provider) {
                if ($provider->GetLanguage() === $item) {
                    $providersName[] = $provider->GetType();
                }
            }
            $languagesDescription[] = array('code' => $item, 'name' => InstanceProvider::getObject()->getLanguageDescriptionByCode($item, $language, Session::get('sid')),
                "providers" => implode(', ', $providersName));
        }
            $xmlSearchParams = $this->generateSearchTranslationsParameters($request);
            OTAPILib2::SearchTranslations($activePageLanguage, Session::get('sid'), $xmlSearchParams, $from, $perpage, $searchTranslations);
            OTAPILib2::makeRequests();
            $totalCount = $searchTranslations->GetResult()->GetTotalCount();

        if (!Session::get('active_lang_search_translations')) {
            foreach ($languagesDescription as $key => $value) {
                Session::set('active_lang_search_translations', $value['code']);
                break;
            }
        }

        $this->tpl->assign('languages', $languagesDescription);
        $this->tpl->assign('searchTranslations', $searchTranslations);
        $this->tpl->assign('path', TPL_PATH);
        $this->tpl->assign('paginator', new Paginator($totalCount, $pageNum, $perpage));

        print $this->fetchTemplate();
    }

    public function saveSearchAction($request) {
        $sid = Session::get('sid');
        $sourceText = $request->getValue('sourceText');
        $key = "stuff:String:Value";
        $translation = $request->getValue('translation');
        $lang = Session::get('active_lang_search_translations');
        $this->otapilib->EditTranslateByKey($sid, $lang, $translation, $key, $sourceText);
        $request->LocationRedirect($this->pageUrl->DeleteKey('do')->AssignDo('search'));
    }

    /**
     * @param RequestWrapper $request
     */
    private function generateSearchParameters($request)
    {
        $xmlParams = new SimpleXMLElement('<TranslationSearchParameters></TranslationSearchParameters>');

        $xmlParams->addChild('IncludeSystemTranslations', 'true');
        if (Session::get('active_lang_translations')) $xmlParams->addChild('Language', Session::get('active_lang_translations'));
        if ($request->get('TranslatableContent')) $xmlParams->addChild('TranslatableContent', $this->escape($request->get('TranslatableContent')));
        if ($request->get('SearchText')) $xmlParams->addChild('SearchText', $this->escape($request->get('SearchText')));

        return $xmlParams->asXML();
    }

    private function generateSearchTranslationsParameters($request)
    {
        $xmlParams = new SimpleXMLElement('<TranslationSearchParameters></TranslationSearchParameters>');

        if (Session::get('active_lang_search_translations')) {
            $xmlParams->addChild('Language', Session::get('active_lang_search_translations'));
        } else {
            if (Session::get('active_lang_translations')) $xmlParams->addChild('Language', Session::get('active_lang_translations'));
        }
        if ($request->request('TranslatableContent')) $xmlParams->addChild('TranslatableContent', $this->escape($request->request('TranslatableContent')));
        if ($request->get('SearchText')) $xmlParams->addChild('SearchText', $this->escape($request->get('SearchText')));

        return $xmlParams->asXML();
    }

    /**
     * @param RequestWrapper $request
     */
    public function serviceUpdateAction($request)
    {
        try {
            $language = Session::getActiveAdminLang();
            $inputLanguage = Session::get('active_lang_translations');
            $translatableContent = $request->getValue('translatableContent');
            $translationId = $request->getValue('translationId');
            $updateData = $this->generateUpdateTranslationData($request);

            $answer = null;
            OTAPILib2::UpdateTranslation($language, Session::get('sid'), $inputLanguage, $translatableContent, $translationId, $updateData, $answer);
            OTAPILib2::makeRequests();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse();
    }

    public function SetSearchTranslationsLangAction($request)
    {
        Session::set('active_lang_search_translations', $request->getValue('lang'));
        $retpath = $request->getValue('retpath');
        $retpath = !empty($retpath) ? $retpath : RequestWrapper::env('HTTP_REFERER', '/admin/');
        $this->redirect($retpath);
    }

    public function SetSearchTranslationsLangUrl($lang)
    {
        $tempUrl = new UrlWrapper();
        return $tempUrl->Set($this->Get())
            ->Add('do', 'SetSearchTranslationsLang')
            ->Add('cmd', 'AdminLanguage')
            ->Add('lang', $lang)
            ->Add('retpath', $this->Get())
            ->Get();
    }

    public function searchUpdateAction($request)
    {
        try {
            $language = Session::getActiveAdminLang();
            $inputLanguage = Session::get('active_lang_search_translations');
            $translatableContent = 'stuff:String:Value';
            $translationId = $request->getValue('translationId');
            $updateData = $this->generateUpdateTranslationData($request);

            $answer = null;
            OTAPILib2::UpdateTranslation($language, Session::get('sid'), $inputLanguage, $translatableContent, $translationId, $updateData, $answer);
            OTAPILib2::makeRequests();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse();
    }


    /**
     * @param RequestWrapper $request
     */
    private function generateUpdateTranslationData($request)
    {
        $xmlParams = new SimpleXMLElement('<TranslationUpdateData></TranslationUpdateData>');

        if ($request->valueExists('Text')) $xmlParams->addChild('Text', $this->escape($request->post('Text')));
        if ($request->valueExists('ResetTranslation')) $xmlParams->addChild('ResetTranslation', $request->post('ResetTranslation'));

        return str_replace('<?xml version="1.0"?>','',$xmlParams->asXML());
    }
}