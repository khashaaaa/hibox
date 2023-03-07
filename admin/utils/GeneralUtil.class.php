<?php

OTBase::import('system.lib.Cache');
OTBase::import('system.lib.cache.Key');
OTBase::import('system.lib.cache.adapter.*');
OTBase::import('system.admin.lib.otapi_providers.LanguageSettings');
OTBase::import('system.admin.lib.RightsManager');

class GeneralUtil
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- LangAdmin::get('Time') на которое будем кешировать
    protected $_template = ''; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = ''; //- путь к шаблону

    protected $periodFilters = array(
        'specified_period',
        'today_period',
        'yesterday_period',
        'current_week_period',
        'last_week_period',
        'last_month_period',
        'last_three_months_period',
        'year_period',
    );

    /**
     * @var HSTemplateDisplay
     */
    protected $tpl;
    protected $defaulAction = null;
    public $inMulti = false;            // Находится ли контроллер в вызове мультикурла
    protected $continuedMulti = false;  // Находится ли контроллер в вызове мультикурла после прерывания

    protected static $rightsDependencies = array();   // Список прав, необходимых для доступа к контроллеру

    /**
     * @var CMS
     */
    protected $cms;
    /**
     * @var OTAPILib
     */
    protected $otapilib;

    protected $authenticationListener;
    /**
     * @var LanguageRepository
     */
    protected $langRepository;
    /**
     * @var LanguageSettings
     */
    protected $languagesProvider;
    /**
     * @var ErrorHandler
     */
    protected $errorHandler;

    /**
     * @var AdminUrlWrapper
     */
    protected $pageUrl;

    private $availableLanguages;
    private $CMSLanguages;

    public function __construct($basicInit = false)
    {
        $this->initOTAPILib();
        $this->initTemplateEngine();
        $this->initCMS();
        $this->authenticationListener = new AuthenticationListener();
        $this->errorHandler = new ErrorHandler($this->authenticationListener);
        $this->pageUrl = new AdminUrlWrapper();
        $this->pageUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

        $cacher = new Cache('CMSLanguages');
        if (! $cacher->has() && CMS::IsFeatureEnabled('MultipleLanguages')) {
            $this->langRepository = new LanguageRepository($this->cms);
            $cacher->set($this->langRepository->GetLanguages());
        }
        $this->CMSLanguages = $cacher->has() ? $cacher->get() : array();

        $this->languagesProvider = new LanguageSettings($this->getOtapilib());

        $this->availableLanguages = $this->languagesProvider->GetActiveLanguages();

    }

    protected function getWebUISettings()
    {
        return $this->languagesProvider->GetLanguages();
    }

    public function onBeforeAction($action)
    {
        if (! $this->_template) {
            $this->_template = strtolower($action);
        }
        if (! $this->_template_path) {
            $this->_template_path = strtolower(get_called_class()) . '/';
        }
    }

    public function getDefaultAction()
    {
        return $this->defaulAction;
    }

    private function initOTAPILib()
    {
        global $otapilib;
        $this->otapilib = $otapilib;
        $this->otapilib->setErrorsAsExceptionsOn();
        $this->otapilib->setUseAdminLangOn();
    }

    private function initTemplateEngine()
    {
        $HSTemplate_options = array(
            'template_path' => TPL_DIR,
            'cache_path'    => TPL_DIR . 'cache',
            'debug'         => false,
        );
        $HSTemplate = new HSTemplate($HSTemplate_options);
        $this->tpl = $HSTemplate->getDisplay($this->_template, true);
    }

    private function initCMS()
    {
        $this->cms = new CMS();
        if (! $this->cms->Check()) {
            throw new DBException(DBException::CONNECTION_ERROR, 0, 'GeneralUtil::initCMS');
        }
    }

    public function respondAjaxError($e, $supressDebug = false)
    {
        $response = array(
            'error' => 1,
        );
        if (is_array($e)) {
            $response['errors'] = $e;
        } elseif ($e instanceof ServiceException) {
            $response['message'] = $e->getErrorMessage();
            $response['code'] = $e->getErrorCode();
            $response['subcode'] = $e->getSubErrorCode();
            if ($response['code'] == 'SessionExpired' && $response['subcode'] == 'SessionExpired') {
                $response['expired'] = 1;
            }
        } elseif ($e instanceof Exception) {
            $response['message'] = $e->getMessage();
        } elseif ($supressDebug && ! OTBase::isTest()) {
            $response['message'] = $e->getMessage();
        } else {
            $response['message'] = $e;
        }

        if (! $supressDebug && OTBase::isTest()) {
            $response['debugLog'] = [
                'title' => '',
                'body' => Debugger::getRender(),
            ];
        }

        header('Content-type: application/json');
        echo json_encode($response);
        die();
    }

    public function sendAjaxResponse(array $response = array(), $checkForOpera = false, $supressDebug = false)
    {
        if (! $supressDebug && OTBase::isTest()) {
            $response['debugLog'] = [
                'title' => '',
                'body' => Debugger::getRender(),
            ];
        }

        // http://jira.rkdev.ru/browse/OTDEMO-752
        // При загрузке файла аяксом Опера не отправляет заголовок application/json
        if ($checkForOpera && (BrowserHelper::isOpera() && !BrowserHelper::isJsonAcceptable())) {
            header('Content-Type: text/plain; charset=utf-8');
        } else {
            header('Content-Type: application/json; charset=utf-8');
        }

        echo json_encode($response);
        die();
    }

    public function throwAjaxError($e, $code = 500)
    {
        $errorCode = $e instanceof ServiceException ? $e->getErrorCode() : ($e->getCode() ? $e->getCode() : 'Internal error');
        header('HTTP/1.1 '. $code .' ' . $errorCode);
        die($e->getMessage());
    }

    public function setErrorAndRedirect($error, $redirectUrl)
    {
        Session::set('error', $error);
        header('Location: '.$redirectUrl);
        throw new Exception($error);
    }

    /**
     * @return \OTAPILib
     */
    public function getOtapilib()
    {
        return $this->otapilib;
    }


    public function setAdminPerPageCookie($perPageValue)
    {
        Cookie::set('__otAdmin_perPageValue', $perPageValue, time()+86400*30);
    }

    public function getAdminPerPageCookie()
    {
        return Cookie::get('__otAdmin_perPageValue');
    }

    public function getTemplateInfo()
    {
        $info = new stdClass();
        $info->template = $this->_template;
        $info->template_path = $this->_template_path;
        return $info;
    }

    public function fetchTemplate()
    {
        $tpl = TPL_DIR . $this->_template_path;
        $tplcustom = TPLCUSTOM_DIR . $this->_template_path;

        $this->pageUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        $this->tpl->assign('PageUrl', $this->pageUrl);
        $this->tpl->assign('CMSLanguages', $this->CMSLanguages);
        $this->tpl->assign('AvailableLanguages', $this->availableLanguages);

        if (file_exists($tplcustom . $this->_template . '.html')) {
            $this->tpl->addTemplate($this->_template, $this->_template.'.html', $tplcustom);
        } else {
            $this->tpl->addTemplate($this->_template, $this->_template.'.html', $tpl);
        }

        $body = $this->tpl->fetch($this->_template);
        $header = $this->fetchHeaderBlock();
        $footer = $this->fetchFooterBlock();

        return $header . $body . $footer;
    }

    public function fetchTemplateWithoutHeaderAndFooter($use_tpl_stack = true)
    {
        $tpl = TPL_DIR . $this->_template_path;
        $tplcustom = TPLCUSTOM_DIR . $this->_template_path;

        $this->pageUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        $this->tpl->assign('PageUrl', $this->pageUrl);
        $this->tpl->assign('CMSLanguages', $this->CMSLanguages);
        $this->tpl->assign('AvailableLanguages', $this->availableLanguages);

        if (file_exists($tplcustom . $this->_template . '.html')) {
            $this->tpl->addTemplate($this->_template, $this->_template.'.html', $tplcustom, $use_tpl_stack);
        } else {
            $this->tpl->addTemplate($this->_template, $this->_template.'.html', $tpl, $use_tpl_stack);
        }

        return $this->tpl->fetch();
    }

    public function fetchBlock($blockName, array $blockVars = array(), $extension = 'php')
    {
        $file = TPL_DIR . $this->_template_path . $blockName.'.'.$extension;
        $customfile = TPLCUSTOM_DIR . $this->_template_path . $blockName.'.'.$extension;

        if (file_exists($customfile)) {
            $tpl = TPLCUSTOM_DIR . $this->_template_path;
        } elseif (file_exists($file)) {
            $tpl = TPL_DIR . $this->_template_path;
        } elseif (file_exists(TPLCUSTOM_DIR . $blockName.'.'.$extension)) {
            $tpl = TPLCUSTOM_DIR;
        } else {
            $tpl = file_exists(TPL_DIR . 'blocks' . $blockName.'.'.$extension) ? TPL_DIR . 'blocks' : TPL_DIR;
        }

        $this->tpl->addTemplate($blockName, $blockName.'.'.$extension, $tpl);

        $this->pageUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        $this->tpl->assign('PageUrl', $this->pageUrl);
        $this->tpl->assign('CMSLanguages', $this->CMSLanguages);
        $this->tpl->assign('AvailableLanguages', $this->availableLanguages);

        if (! empty($blockVars)) {
            foreach ($blockVars as $key => $value) {
                $this->tpl->assign($key, $value, $blockName);
            }
        }

        return $this->tpl->fetch($blockName);
    }

    public function fetchHeaderBlock()
    {
        $adminLanguage = new AdminLanguage(true);

        return $this->fetchBlock('header', array(
            'activeLanguages' => $adminLanguage->getActiveLanguages($this->availableLanguages)
        ));
    }

    public function getHtmlForSettingsSmsService()
    {
        $smsServerInfoList = [];

        try {
            OTAPILib2::GetSmsServiceSettings(Session::getActiveAdminLang(), Session::get('sid'), 'True', $response);
            OTAPILib2::makeRequests();

            $smsServerInfoList = $response->GetResult()->GetRawData();
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }

        $pageUrl = new UrlWrapper();
        $pageUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        $pageUrl->DeleteKey('cmd')->Add('cmd', 'SiteConfiguration')->DeleteKey('do')->Add('do', 'saveSmsServiceSettings');

        return General::viewFetch('site_config/system/sms_service', array(
            'path' => TPL_PATH,
            'vars' => array(
                'smsServiceInfoList' => $smsServerInfoList,
                'pageUrl' => $pageUrl->Get(),
            ),
        ));
    }

    public function getHtmlForSettingsEmailServerInfo()
    {
        if (!RightsManager::hasRight('EmailServerManagement')) {
            return false;
        }
        $emailServerInfoList = array();
    
        try {
            OTAPILib2::GetEmailServerInfoList(Session::getActiveAdminLang(), Session::get('sid'), $emailServerInfoList);
            OTAPILib2::makeRequests();
            if ($emailServerInfoList && $emailServerInfoList->GetResult()->GetContent()->GetItem()) {
                $emailServerInfoList = $emailServerInfoList->GetResult()->GetContent()->GetItem();
            }
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }
    
        $pageUrl = new UrlWrapper();
        $pageUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        $pageUrl->DeleteKey('cmd')->Add('cmd', 'SiteConfiguration')->DeleteKey('do')->Add('do', 'getEmailServerInfo');
        
        return General::viewFetch('site_config/system/email_server', array(
            'path' => TPL_PATH,
            'vars' => array(
                'emailServerInfoList' => $emailServerInfoList,
                'PageUrl' => $pageUrl,
            ),
        ));
    }    
    
    private function checkSmtpSettings() 
    {
        $cookie = Cookie::get('email_settings_checked');
        $server = General::getConfigValue('email_smtp_adress');
        if (empty($server) && empty($cookie) && ! ($this->pageUrl->GetCmd() == 'SiteConfiguration' && $this->pageUrl->GetAction() == 'system')) {
            Cookie::set('email_settings_checked', '1', time() + 3600);
            return General::viewFetch('site_config/system/email_settings_popup', array(
                'path' => TPL_PATH,
                'vars' => array(
                    'emailSettings' => $this->getHtmlForSettingsEmailServerInfo(),
                ),
            ));
        }
        return '';
    }

    public function fetchFooterBlock()
    {
        $smtpSettings = $this->checkSmtpSettings();
        $debugLog = '';
        if (OTBase::isTest()) {
            $debugLog = Debugger::getRender();
        }

        return $this->fetchBlock('footer', array(
            'debugLog' => $debugLog,
            'smtpSettings' => $smtpSettings
        ));
    }

    public function showErrorPage()
    {
        $tpl = TPL_DIR . 'error';
        $this->assign('header');
        $this->assign('footer');
        $this->tpl->addTemplate('error', 'error.html', $tpl);
        return $this->tpl->fetch();
    }

    /**
     * @return \AdminUrlWrapper
     */
    public function getPageUrl()
    {
        return $this->pageUrl;
    }

    private function renderBlock($blockName)
    {
        ob_start();
        $this->pageUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        $PageUrl = $this->pageUrl;
        $CMSLanguages = $this->CMSLanguages;
        $AvailableLanguages = $this->availableLanguages;
        require TPL_ABSOLUTE_PATH.$blockName.'.php';
        $block = ob_get_contents();
        ob_end_clean();
        return $block;
    }

    private function assign($part)
    {
        ob_start();
        require TPL_ABSOLUTE_PATH.$part.'.php';
        $header = ob_get_contents();
        $this->tpl->assign(ucfirst($part), $header);
        ob_end_clean();
    }

    public function setActiveLanguageForCurrentPage($request, $language)
    {
        Session::set('active_lang_admin_'.$request->get('cmd'), $language);
    }

    public function getActiveLanguageForCurrentPage($request)
    {
        return Session::get('active_lang_admin_'.$request->get('cmd'));
    }

    public function redirect($url)
    {
        header('Location: ' . $url);
        die();
    }

    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES);
    }

    public function startMulti($continued = false)
    {
        $this->otapilib->InitMulti();
        $this->inMulti = true;
        Session::set('isMultiCurlRunning', true);
        $this->continuedMulti = $continued;
    }

    public function doMulti()
    {
        $this->otapilib->MultiDo();
        $this->inMulti = false;
        Session::clear('isMultiCurlRunning');
    }

    public function stopMulti()
    {
        $this->otapilib->StopMulti();
        $this->inMulti = false;
        Session::clear('isMultiCurlRunning');
        $this->continuedMulti = false;
    }

    public function getMultiCurlActions()
    {
        if (isset($this->multiCurlActions) && is_array($this->multiCurlActions)) {
            return $this->multiCurlActions;
        }
        return array();
    }

    protected function getPageDisplayParams($request, $defaultItemsPerPage = 10)
    {
        $perpageCookie = $this->getAdminPerPageCookie();
        $perpage = $request->getValue('perpage', $perpageCookie);
        $perpage = $perpage ? $perpage : $defaultItemsPerPage;
        $this->setAdminPerPageCookie($perpage);
        $page = $request->getValue('page', 0);
        if (! $this->inMulti) {
            if ($perpageCookie && $perpage > $perpageCookie && $page > 0) {
                $this->redirect($this->pageUrl->deleteKey('page')->get());
            }
        }
        $offset = ($page > 1) ? ($page - 1) * $perpage : 0;
        return array(
            'number'    => $page,
            'offset'    => $offset,
            'limit'     => $perpage,
        );
    }

    protected function getFrameLimits($page, $perpage, $totalCount)
    {
        if ($page <= 0) {
            $page = 1;
        }
        $start = ($page - 1) * $perpage + 1;
        $end = ($page * $perpage >= $totalCount || $totalCount < $perpage) ? $totalCount : $page * $perpage;
        return array(
            'start' => $start,
            'end'   => $end,
        );
    }

    protected function parseTextWithUrl($text)
    {
        return TextHelper::parseTextWithUrl($text);
    }

    protected static function isActionAllowed()
    {
        if (RightsManager::isSuperAdmin()) {
            return true;
        }

        foreach (static::$rightsDependencies as $right) {
            if (! RightsManager::hasRight($right)) {
                return false;
            }
        }
        return true;
    }

    public function getPeriodFilters()
    {
        $periods    = array();
        $startTime  = time();
        $endTime    = time();
        foreach ($this->periodFilters as $period) {
            $dateObj = new DateTime();
            switch ($period) {
                case 'today_period':
                    $startTime  = time();
                    $endTime    = time();
                break;
                case 'yesterday_period':
                    $startTime = strtotime($dateObj->format('Y/m/d H:i:s') . ' - 1day');
                    $endTime = $startTime;
                break;
                case 'current_week_period':
                    $diff = $dateObj->format('w') - 1;
                    $startTime = strtotime($dateObj->format('Y/m/d H:i:s') . ' - ' . $diff . 'day');
                    $endTime   = time();
                break;
                case 'last_week_period':
                    $diff = $dateObj->format('w');
                    $startTime = strtotime($dateObj->format('Y/m/d H:i:s') . ' - ' . ($diff + 6) . 'day');
                    $endTime = strtotime($dateObj->format('Y/m/d H:i:s') . ' - ' . $diff . 'day');
                break;
                case 'last_month_period':
                    $startTime = strtotime($dateObj->format('Y/m/1 H:i:s') . ' - 1month');
                    $endTime = mktime(23, 59, 59, date('m', $startTime), date('t', $startTime), date('Y', $startTime));
                break;
                case 'last_three_months_period':
                    $startTime = strtotime($dateObj->format('Y/m/1 H:i:s') . ' - 3month');
                    $endTime = strtotime($dateObj->format('Y/m/d') . ' - 1month');
                    $endTime = mktime(23, 59, 59, date('m', $endTime), date('t', $endTime), date('Y', $endTime));
                break;
                case 'year_period':
                    $startTime = strtotime($dateObj->format('Y/1/1 H:i:s'));
                    $endTime   = time();
                break;
            }
            $dateObj->setTimestamp($startTime);
            $startDate = $dateObj->format('F d, Y H:i:s');
            $dateObj->setTimestamp($endTime);
            $endDate = $dateObj->format('F d, Y H:i:s');
            $periods[$period] = array(
                'start' => $startDate,
                'end'   => $endDate,
            );
        }
        return $periods;
    }

    public static function renderMetaUISettingsByEntity($request, $entityName)
    {
        $lang = Session::getActiveAdminLang();
        $sid = Session::get('sid');
        $updateSettingsUrl = '?cmd=MetaUiUtil&do=updateSettings&metaEntity=' . $entityName;
        $settings = new OtapiAnswer(null);

        try {
            $entities = MetaUI::GetMetaEntities($lang);
            /** @var OtapiMetaEntityInfo $entity */
            $entity = isset($entities[$entityName]) ? $entities[$entityName] : new OtapiMetaEntityInfo(null);

            $additionalParameters = array();
            if ($entity->GetAdditionalParameters()) {
                foreach ($entity->GetAdditionalParameters()->GetParameter() as $parameter) {
                    $additionalParameters[$parameter] = $request->get($parameter);
                }
            }
            if (!empty($additionalParameters)) {
                $updateSettingsUrl .= '&' . http_build_query($additionalParameters);
            }

            OTAPILib2::simpleRequest($entity->GetGetMethod(), array_merge(
                array(
                    'language' => $lang,
                    'sessionId' => $sid,
                    'includeMetaInfo' => 'true',
                ),
                $additionalParameters
            ), $settings);
            OTAPILib2::makeRequests();
        } catch (Exception $e) {
            ErrorHandler::registerError($e);
        }

        $settings = isset($settings->GetRawData()->Result) ? $settings->GetRawData()->Result : null;

        return MetaUI::render($settings, $updateSettingsUrl);
    }

    /**
     * @param RequestWrapper $request
     * @return string
     */
    public function getActiveLang($request)
    {
        $languagesProvider = new LanguageSettings($this->otapilib);
        $cmd = RequestWrapper::get('cmd');

        if (Session::get('active_lang_' . strtolower($cmd))) {
            return Session::get('active_lang_' . strtolower($cmd));
        } else {
            return key($languagesProvider->GetActiveLanguages());
        }
    }
}
