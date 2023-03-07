<?php

class General
{
    static $siteConf = [];
    static $enabledFeatures = [];
    static $otapiRequests = [];
    static $isContent = false;
    static $showVendorSalesVolume = false;
    private static $cms = null;
    private static $scriptName = null;
    private static $statisticGroupId = null;
    static $_page = false;
    static $baseTheme = 'lite';
    static $params = [];

    public static function rrmdir($dir, $removeDir = true, $options = array())
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if (!empty($options['skipFiles']) && in_array($dir . "/" . $object, $options["skipFiles"])) continue;
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        self::rrmdir($dir . "/" . $object, true, $options);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            if ($removeDir && !count(glob($dir . '/*')))
                rmdir($dir);
        }
    }

    public static function rCopy($from, $to) {
        if (is_dir($from)) {
            @mkdir($to);
            $d = dir($from);
            while (false !== ($entry = $d->read())) {
                if ($entry == "." || $entry == "..") continue;
                self::rCopy("$from/$entry", "$to/$entry");
            }
            $d->close();
        }
        else copy($from, $to);
    }

    public static function mail_utf8($to, $from_user, $from_email,
                       $subject = '(No subject)', $message = '', $convertNewLines = false, $options = array())
    {
        try {
            if(defined('CFG_DENY_MAIL_SENDING')) {
                return true;
            }
            if ($convertNewLines) {
                $message = nl2br($message);
            }
            $sender = new MailSender($from_user, $from_email, $subject, $message);
            if (self::checkPHPVersion('5.3.0')) {
                if (self::getConfigValue('email_smtp_adress')) {
                    $sender->setSMTP(
                        self::getConfigValue('email_smtp_security'),
                        self::getConfigValue('email_smtp_adress'),
                        self::getConfigValue('email_smtp_port'),
                        self::getConfigValue('email_smtp_user'),
                        self::getConfigValue('email_smtp_pass')
                    );
                }
            }
            $sender->addAddress($to);
            $sender->sendMail();
        } catch (Exception $e){
        	if (isset($options['useException']) && $options['useException'] == true) {
        		throw $e;
        		return;
        	}
            if (OTBase::isTest()) {
                Session::setError($e->getMessage());
            } else {
                Session::setError(Lang::get('Error_in_send_mails'));
            }
        }
    }

    public static function checkPHPVersion($min_version)
    {
        if (version_compare(phpversion(), $min_version, '>=')) {
            return true;
        } else {
            return false;
        }
    }

    public static function mail_utf8_txt($to, $from_user, $from_email,
                       $subject = '(No subject)', $message = '')
    {
        //Пишем в файл
        $fp = fopen("notyfy_tests.dat", "w+");
        fwrite($fp, $to."<br>".$subject."<br>".$message."<br><br><br>");
        fclose($fp);
    }

    public static function sessionExpiredHandle($show = true)
    {
        global $otapilib;
        if ($otapilib->error_message == 'SessionExpired' || !Session::isAuthenticated()) {
            header('Location: /?p=login');
            die();
        } elseif ($show) {
            show_error();
        }
    }

    public static function loadEnabledFeatures(){
        $enabledFeatures = InstanceProvider::getObject()->GetFeatures();
        self::$enabledFeatures = $enabledFeatures;
    }

    static public function IsFeatureEnabled($feature)
    {
        return in_array($feature, self::$enabledFeatures);
    }

    public static function getAliasQuery()
    {
        return (isset($_GET['q'])) ? rtrim($_GET['q'], '/') : '';
    }

    /**
     * Обеспечивает наличие q, p, action параметров
     * @return string - запрашиваемый адрес страницы
     */
    public static function prepareRequest()
    {
        $requestedUri = '';

        if (General::IsFeatureEnabled('Seo2')) {
            $scriptName = trim(RequestWrapper::env('SCRIPT_NAME'), '/');
            // брать запрашиваемый uri, для запросов к index.php
            if ($scriptName === 'index.php') {
                $requestedUri = trim(RequestWrapper::path(), '/');
                // убрать index.php из запрашиваемого uri
                $requestedUri = str_replace('index.php', '', $requestedUri);
            }
        }

        if (!General::IsFeatureEnabled('Seo2') || empty($requestedUri)) {
            if (isset($_GET['q'])) {
                $requestedUri = rtrim($_GET['q'], '/');
            } elseif (isset($_GET['p'])) {
                $requestedUri = rtrim($_GET['p'], '/');
            }
        }

        if (!empty($requestedUri)) {
            $_GET['q'] = $requestedUri;

            $aliasTmp = explode('/', $requestedUri);
            $_GET['p'] = $aliasTmp[0];
            $_GET['action'] = (isset($aliasTmp[1])) ? $aliasTmp[1] : '';
        }

        return $requestedUri;
    }

    public static function prepareAliases($alias)
    {
        if (!empty($alias)) {
            $cms = General::getCms();
            $alias = explode('/', $alias);

            switch ($_GET['p']) {
                case 'category':
                case 'subcategory':
                    if (isset($alias[1])) {
                        $seoRepository = new SeoCategoryRepository($cms);
                        list($cid, $needRedirect) = $seoRepository->parseCategoryIdFromAlias($alias[1]);
                        if ($needRedirect) {
                            RequestWrapper::LocationRedirect($cid, 301);
                        }
                        $_GET['cid'] = $cid;
                        $_REQUEST['cid'] = $cid;
                    }
                    break;
                case 'post':
                    if (count($alias) > 1 && ! array_key_exists('id', $_GET)) {
                        $blogRepository = new DigestRepository($cms);
                        list($id, $needRedirect) = $blogRepository->parseIdFromAlias($alias[1]);
                        if ($needRedirect) {
                            RequestWrapper::LocationRedirect($id);
                        }
                        $_GET['id'] = $id;
                    }
                    break;
            }
        }
    }

    public static function getLangs(){
        global $otapilib;

        $langsObject = InstanceProvider::getObject()->GetLanguageInfoList();
        $languages = $otapilib->GetLanguageInfoList($langsObject->asXML());

        return $languages;
    }

    public static function validateLangs($langs){
        if ($langs === false) {
            return ;
        }
        $valid = false;
        if( @$_SESSION['active_lang'] ){
            foreach($langs as $l){
                if($l['Name'] == $_SESSION['active_lang']){
                    $valid = true;
                    break;
                }
            }
        }
        if ($langs && count($langs) > 0 && (!isset($_SESSION['active_lang']) || empty($_SESSION['active_lang']) || !$valid)) {
            Session::setActiveLang(User::getObject()->getActiveLang());
        }
        $GLOBALS['langs'] = $langs;
        Lang::getTranslations();
    }

    public static function setConf(){
        $cms = new CMS();
        if(!$cms->Check())
            return ;
        if(!$cms->checkTable('site_config'))
            return ;

        $res = $cms->getSiteConfig();
        self::$siteConf = $res[1];
        $defaultSettings = self::getFilteredDefaultSettings();
        self::$siteConf = array_merge($defaultSettings, self::$siteConf);

        // TODO: price_round_decimals оставили для старых сайтов, в платформе не используется
        self::$siteConf['price_round_decimals'] = self::getNumConfigValue('price_rounding');
        self::$showVendorSalesVolume = defined('CFG_SHOW_VENDOR_SALES') ? CFG_SHOW_VENDOR_SALES : false;

        if (self::getConfigValue('CFG_PREFIX_REPLACE_ORD')) {
            @define('CFG_PREFIX_REPLACE_ORD', self::getConfigValue('CFG_PREFIX_REPLACE_ORD'));
        }
    }

    private static function getFilteredDefaultSettings()
    {
        // определяем дефолтные значения для настроек, согласно подключенной теме
        $fileDefaultSettings = self::getThemeDir() . '/config/defaultSettings.php';
        $defaultSettings = array();

        if (file_exists($fileDefaultSettings)) {
            $defaultSettings = require_once $fileDefaultSettings;

            foreach (self::$siteConf as $key => $value) {
                foreach ($defaultSettings as $dKey => $dValue) {
                    if (preg_match('/' . $key . '.*/', $dKey)) {
                        unset($defaultSettings[$dKey]);
                    }
                }
            }
        }

        return $defaultSettings;
    }

    public static function init()
    {
        self::getCms();
        self::setConf();
        $langs = self::getLangs();
        self::validateLangs($langs);
        self::loadEnabledFeatures();
        self::defineParams();

        // задать $_GET[q|p|action],
        // в новой оставлен для совместимости со старыми ссылками
        $requestedUri = General::prepareRequest();
        if (self::getConfigValue('is_old_platform')) {
            self::prepareAliases($requestedUri);
        }
        $GLOBALS['pagetitle'] = '';

        if (! defined('CFG_SITE_NAME')) {
            define('CFG_SITE_NAME', self::getConfigValue('site_name'));
        }

        // определяем константу SCRIPT_NAME или редиректим на верный URL
        self::defineScriptName();
        self::setGroupId(SCRIPT_NAME);
        self::setScriptName(SCRIPT_NAME);


        // если есть реферальный ключ - запомним его
        ReferalSystem::defineReferrerKey();

        General::setPageSeo();

        Plugins::runSerialEvent('onAfterGeneralInit');
    }

    public static function getCms()
    {
        // проверяем актуальность экземпляра
        if (null === self::$cms) {
            $cms = new CMS();
            if (! $cms->Check()) {
                throw new DBException(DBException::CONNECTION_ERROR, 0, 'self::getCms');
            }
            self::$cms = $cms;
        }
        // возвращаем созданный или существующий экземпляр
        return self::$cms;
    }

    // определяем константу SCRIPT_NAME или редиректим на верный URL
    // TODO: редирект не совсем корректен в рамках текущего метода, редирект должен быть вызван из фронт контроллеров
    public static function defineScriptName()
    {
        if (self::getConfigValue('is_old_platform')) {
            // проверяем SEO и редиректим или отдаем 404 по необходимости
            if (self::isCorrectUrl()) {
                define('SCRIPT_NAME', ((isset($_GET['p'])) && ($_GET['p'] != '')) ? $_GET['p'] : 'index');
            }
        } else {
            // пропускаем ситуацию с главной страницей, админкой и скриптами не фронт контроллера
            if (! isset($_GET['q'])) {
                define('SCRIPT_NAME', ((isset($_GET['p'])) && ($_GET['p'] != '')) ? $_GET['p'] : 'index');
                return SCRIPT_NAME;
            }

            // логика для платформы 1.5 за исключением главной страницы
            $isPrettyUrl = false;
            if (
                (strpos($_SERVER['REQUEST_URI'], '?q=') === false) &&
                (strpos($_SERVER['REQUEST_URI'], '&q=') === false) &&
                (strpos($_SERVER['REQUEST_URI'], '?p=') === false) &&
                (strpos($_SERVER['REQUEST_URI'], '&p=') === false)
            ) {
                $isPrettyUrl = true;
            }

            if (self::IsFeatureEnabled('Seo2')) {
                if (!$isPrettyUrl && $_SERVER['REQUEST_METHOD'] === 'GET') {
                    // редирект на верный ЧПУ URL
                    $url = UrlGenerator::getUrl($_GET['q'], array('includeGet' => true));
                    header('Location: ' . $url, true, 301 );
                    die();
                }
                define('SCRIPT_NAME', ((isset($_GET['p'])) && ($_GET['p'] != '')) ? $_GET['p'] : 'index');

            } else {
                if ($isPrettyUrl) {
                    define('SCRIPT_NAME', '404');
                } else {
                    define('SCRIPT_NAME', ((isset($_GET['p'])) && ($_GET['p'] != '')) ? $_GET['p'] : 'index');
                }
            }
        }
    }

    public static function defineParams()
    {
        $params = [];

        $paramsFile = self::getThemeDir() . '/config/params.php';
        if (file_exists($paramsFile)) {
            $params = require_once $paramsFile;
        }

        self::$params = $params;
    }

    public static function generateUrl($p, $params){
        $method = "generate".ucfirst($p)."Url";
        return UrlGenerator::$method($params);
    }

    public static function getCurrencyPrice($item, $params = array())
    {
        $price = self::getPriceOfConvertedPriceList($item['Price']['ConvertedPriceList']);

        return self::getHtmlPrice($price, $params);
    }

    public static function getCurrencyPromoPrice($item, $params = array())
    {
        $price = self::getPriceOfConvertedPriceList($item['PromotionPrice']);

        return self::getHtmlPrice($price, $params);
    }

    public static function getPriceOfConvertedPriceList($convertedPriceList)
    {
        $priceSign = User::getObject()->getCurrencyCode();

        foreach ($convertedPriceList as $v) {
            if ($v['Code'] == $priceSign) {
                $price = $v;
                break;
            }
        }

        return (isset($price)) ? $price : array();
    }

    /*
     * @param array $data элемент массива ConvertedPrice
     */
    public static function getHtmlPrice($data, $params = array())
    {
        // если нет курсов валют или цена не пришла
        if (! is_array($data) || empty($data)) {
            return '';
        }

        $price = self::priceFormat($data['Val'], self::getNumConfigValue('price_rounding'));
        $currency = $data['Sign'];
        if (isset($params['addItemprop']) && $params['addItemprop']) {
            $price = '<span itemprop="price" content="' . number_format($data['Val'], 2, '.', '') . '" title="' . $price . " " . $currency . '"dir="ltr">' . $price . '</span>';
        }

        if (isset($params['addItemprop']) && $params['addItemprop']) {
            $currency = '<span itemprop="priceCurrency" content="' . $data['Code'] . '">' . $currency . '</span>';
        }

        return $price . '&nbsp;' . $currency;
    }

    public static function getSeoText($id, $language = 'ru'){
        $cms = new CMS();
        $cms->Check();
        $cms->checkTable('site_categories_seo_texts');
        $id = General::getCms()->escape($id);
        return General::getCms()->querySingleValue('SELECT text FROM `site_categories_seo_texts` WHERE `category_id`="'.$id.'"  AND `lang_code`="'.$language.'"');
    }

    public static function getSeoCategoryImage($id)
    {
        if (isset($GLOBALS['CategoryImage'][$id])) {
            return $GLOBALS['CategoryImage'][$id];
        }
        return false;
    }

    public static function rus2translit($string) {
        $converter = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        );
        return strtr($string, $converter);
    }

    public static function log($type, $message){
        $cms = new CMS();
        $cms->Check();

        $cms->checkTable('site_logs');
        $t = time();
        General::getCms()->query("INSERT INTO `site_logs` SET `log_type` = '$type', `added` = '$t', `text` = '$message'");
    }

    // format price
    public static function priceFormat($price, $decimals) {
        return is_numeric($price) ? number_format($price, $decimals, '.', '&nbsp;') : '';
    }

    // Парсинг логов.
    public static function GetArrayLogs($traceLogs)
    {
        // Обработка.
        $logArray = array();
        // Учет вложености  - был массив теперь проще 2 переменные.
        $firstLevelLog = 0; // Первая.
        $secondLevelLog = 0;  // Вторая.

        foreach ($traceLogs as $time => $serviceLog) {
            $serviceLog = preg_replace('#[\r\n]+#si', "\n", $serviceLog);
            $lines = array_filter(explode("\n", $serviceLog));
            foreach ($lines as $line) {
                if (substr_count($line, "|") == 0) {
                    if (strpos($line, date('Y-m-d')) !== false) {
                        $firstLevelLog++;
                        $secondLevelLog = 0; // Очищаем вложенность у текущего.
                        if (timer::isMysqlQuery($line)) {
                            preg_match('/MYSQL_(?P<method>[a-zA-Z0-9]+).*<!--time-->(?P<time>.*)<!--\/time-->/', trim($line), $match);
                        } else {
                            preg_match('/OTAPIlib2{0,1}\-\>(?P<method>[a-zA-Z0-9]+).*<!--time-->(?P<time>.*)<!--\/time-->/', trim($line), $match);
                        }

                        $logArray[$firstLevelLog]['time'] = isset($match['time']) ? $match['time'] : 0;
                        $logArray[$firstLevelLog]['method'] = isset($match['method']) ? $match['method'] : '';
                    } elseif (preg_match('/[^0-9]*(?P<time>[0-9\.]+).*overhead: (?P<overhead>[0-9\.]+)/is', $line, $match)) {
                        $secondLevelLog++;
                        $logArray[$firstLevelLog][$secondLevelLog]['time']     = isset($match['time']) ? $match['time'] : 0;
                        $logArray[$firstLevelLog][$secondLevelLog]['method']   = isset($logArray[$firstLevelLog]['method']) ? $logArray[$firstLevelLog]['method'] : '';
                        $logArray[$firstLevelLog][$secondLevelLog]['overtime'] = isset($match['overhead']) ? $match['overhead'] : 0;
                    }
                }
            }
        }
        return $logArray;
    }

    public static function getSiteConfig($name) {
        $cms = new CMS();
        if(!$cms->Check())
            return;
        if(!$cms->checkTable('site_config'))
            return;

        $res = $cms->getSiteConfigMultipleLanguages($name);
        if ($res) {
            return $res;
        } else {
            return '';
        }
    }

    public static function getSiteNumConfig($name) {
        $cms = new CMS();
        if(!$cms->Check())
            return;
        if(!$cms->checkTable('site_config'))
            return;

        $res = $cms->getSiteConfigMultipleLanguages($name);
        if ($res===false)
            return false;

        if ($res)
            return $res;

        if ($res==0)
            return '0';
    }

    public static function getConfigValue($name, $default = null, $allowEmpty = true, $lang = false)
    {
        // если есть переменная для текущего языка, иначе ищем переменную для всех языков
        if ($lang === '' || strlen($lang) > 0) {
            $name_lang = $name . '_' . $lang;
        } else {
            $name_lang = $name . '_' . Session::getActiveLang();
        }
        $key = array_key_exists($name_lang, self::$siteConf) ? $name_lang : $name;

        $result = array_key_exists($key, self::$siteConf) ? self::$siteConf[$key] : $default;
        if ($allowEmpty) {
            return $result;
        } else {
            return !empty($result) ? $result : $default;
        }
    }

    public static function getNumConfigValue($name, $default = null, $allowEmpty = true, $lang = false)
    {
        $number = self::getConfigValue($name, $default, $allowEmpty, $lang);
        if ($number) {
            return $number;
        } elseif (! is_null($number) && ($number === 0 || $number === "0")) {
            return '0';
        } else {
            return $default;
        }
    }

    public static function getAllConfigValues()
    {
        return self::$siteConf;
    }

    public static function isCorrectUrl()
    {
        $noRedirect = array('menushortajax', 'itemcomments', 'itemdescriptiontranslated', 'delete_from_basket', 'pay');

        //Если нет $_GET['q'] и включен SEO, но есть $_GET['p'] -
        //то генерация верной ссылки и релирект, кроме AJAX заропосв
        //menushortajax, itemcomments? itemdescriptiontranslated, delete_from_basket
        if (self::IsFeatureEnabled('Seo2')
            && empty($_POST)
            && empty($_GET['q'])
            && (! empty($_GET['p']))
            && (! in_array($_GET['p'], $noRedirect))
        ) {
            $url = UrlGenerator::redirectToSeoUrl($_GET);
            header( 'Location: ' . $url, true, 301 );
            die;
        }
        return true;
    }

    public static function IsNewPlatform($controller, $action)
    {
        if (!self::getConfigValue('is_old_platform')) {

            // если метод Default проверить $controller на совпадение с маршрутом
            if ($action == 'Default') {
                $res = self::onNewPlatformScript($controller);
                if ($res) {
                    return true;
                }
            }

            $controller = $controller . 'Controller';
            $action = $action . 'Action';

            if (class_exists($controller, true) && method_exists($controller, $action)) {
                return true;
            }
        }

        return false;
    }

    public static function isTechnicalWorks()
    {
        $available = false;
        // проверяем доступность сайта - иначе редиректим на страницу "Сайт закрыт на технические работы"
        if (General::getConfigValue('site_temporary_unavailable', false)) {
            $availableAliases = array('site_unavailable', 'logout', 'cron');
            if (! in_array(self::getScriptName(), $availableAliases)) {
                $available = true;
            }
        }
        return $available;
    }

    public static function setPageSeo()
    {
        // пытаемся заполнить СЕО данные для текущей страницы
        $cRep = new ContentRepository(self::getCms());
        $page = $cRep->GetPageByAlias(self::$scriptName);

        if ($page) {
            self::$_page['is_index'] = $page['is_index'];
            self::$_page['title'] = (!empty($page['pagetitle'])) ? $page['pagetitle'] : $page['title'];
            self::$_page['seo_keywords'] = (!empty($page['seo_keywords'])) ? $page['seo_keywords'] : '';
            self::$_page['seo_description'] = (!empty($page['seo_description'])) ? $page['seo_description'] : '';
            self::$_page['title_h1'] = (!empty($page['title_h1'])) ? $page['title_h1'] : General::$_page['title'];
            self::$_page['title_h1'] = TextHelper::escape(self::$_page['title_h1']);
            self::$_page['text'] = $page['text'];

            // обнуляем дефолтное содержимое для старых версий
            if ($page['text'] === '...' || $page['text'] === '<p>...</p>') {
                self::$_page['text'] = '';
            }
        }
    }

    public static function getScriptName()
    {
        return self::$scriptName;
    }

    private static function setScriptName($scriptName)
    {
        self::$scriptName = $scriptName;
    }

    /*
     * TODO: удалить этот метод т.к. не используется
     * @param RequestWrapper $request
     */
    public static function runApplication($request)
    {
        // p - формируется в self::init()
        $scriptName = $request->get('p', 'index');
        self::$scriptName = $scriptName;

        if (General::isTechnicalWorks() && !Session::get('sid')) {
            RequestWrapper::LocationRedirect('/?p=site_unavailable');
        }
        General::setPageSeo();

        $xml = self::getXmlConfigController();

        // проверяем урл в алиасах
        $script = $xml->xpath('script[@url="' . $scriptName . '"]');
        if (empty($script)) {
            // TODO: определить $script как ContentController
            throw new Exception('Error: page not found (General::runApplication)');
        }
        $layout = (isset($script[0]->layout)) ? (string)$script[0]->layout : 'singleController';

        // запускаем контроллер и подставляем результат в шаблон
        $result = self::runControllersByScript($script[0]);

        Plugins::runSerialEvent('onGeneralRunApplication', array(
            'page' => $result
        ));

        $layoutFile = View::getFilePath(CFG_VIEW_ROOT, '/layout/'. $layout . '.php');
        require_once $layoutFile;
    }

    public static function getXmlConfigController()
    {
        $cacher = new FileAndMysqlMemoryCache(new CMS());

        $cacheId = 'General:xmlConfigController';
        if ($cacher->Exists($cacheId)) {
            $xml = $cacher->GetCacheEl($cacheId);
        } else {
            $xml = file_get_contents(CFG_APP_ROOT . '/config/controller.xml');
            $cacher->AddCacheEl($cacheId, 86400, $xml);
        }

        $config = simplexml_load_string($xml);

        return $config;
    }

    private static function runControllersByScript($script)
    {
        $controller = (isset($script->controller)) ? (string)$script->controller : ucfirst((string)$script['name']);

        if (isset($script->controller['action'])) {
            $action = (string)$script->controller['action'];
        } elseif (isset($_GET['action']) && !empty($_GET['action'])) {
            $action = $_GET['action'];
        } else {
            $action = 'default';
        }

        $parameters = array(
            'cache' => (isset($script->controller['cache']) ? (bool)(int)$script->controller['cache'] : false),
            'cache_time' => (isset($script->controller['cache_time']) ? (bool)(int)$script->controller['cache_time'] : 3600),
            'vars' => self::getParametersByRouting($script),
        );

        return self::runController($controller, $action, $parameters);
    }

    public static function getParametersByRouting($script)
    {
        $request = new RequestWrapper();
        $uri = $request->get('q', '');
        $params = explode('/', $uri);

        // пропускаем первый (controller) и второй (action) параметры uri
        // или убираем столько параметров, сколько определено в конфигурации
        $uriSliceParam = (isset($script->controller['uriSliceParam'])) ? (int)$script->controller['uriSliceParam'] : 2;
        return array_slice($params, $uriSliceParam);
    }

    public static function runController($controller, $do = 'default', $parameters = array())
    {
        $method_name = $do . 'Action';
        $controller_name = $controller . 'Controller';

        if (! class_exists($controller_name, true)) {
            throw new Exception('Unknown Request '.$controller_name.'::'.$method_name);
        }

        /**
         * @var $cmd GeneralContoller
         */
        $cmd = new $controller_name();
        if (! method_exists($cmd, $method_name)) {
            throw new Exception('Unknown Request '.$controller_name.'::'.$method_name);
        }

        // генерируем ответ контроллера
        $result = '';
        $vars = (isset($parameters['vars'])) ? $parameters['vars'] : array();

        // проверка плагинов
        $pluginResult = Plugins::runEvent('on' . $controller_name . $method_name, $vars);

        if ($pluginResult !== '') {
            $result = $pluginResult;
        } else {
            // проверяем использование кеша
            if (isset($parameters['cache']) && $parameters['cache']) {
                $cacher = new FileAndMysqlMemoryCache(new CMS());

                $cacheId = 'Controller:' . $controller . $do;
                $cacheId = (isset($parameters['cacheHash'])) ? $cacheId . $parameters['cacheHash'] : $cacheId;
            }

            // проверяем кеш
            if (isset($cacher) && isset($cacheId) && $cacher->Exists($cacheId)) {
                $result = $cacher->GetCacheEl($cacheId);
            } else {
                if (isset($parameters['vars'])) {
                    $args = $cmd->bindActionParams($method_name, $parameters['vars']);
                    $result = call_user_func_array(array($cmd, $method_name), $args);
                } else {
                    $result = $cmd->$method_name();
                }

                // проверяем необходимость записи в кеш
                if (isset($cacher) && isset($cacheId)) {
                    $cacheTime = isset($parameters['cache_time']) ? $parameters['cache_time'] : 3600;
                    $cacher->AddCacheEl($cacheId, $cacheTime, $result);
                }
            }
        }

        $before = Plugins::runEvent('onBefore' . $controller_name . $method_name, $vars);
        $after = Plugins::runEvent('onAfter' . $controller_name . $method_name, $vars);

        $result = $before . $result . $after;

        return $result;
    }
    
    public static function runBlock($name)
    {
        $block = new $name();
        return $block->Generate();
    }

    public static function runBlocks($template, $blocks, $withoutHeaderFooter = true)
    {
        $vars = [];
        foreach ($blocks as $blockName) {
            $vars[$blockName]= General::runBlock($blockName);
        }

        if ($withoutHeaderFooter && !array_key_exists('HeaderNew', $vars)) {
            $vars['HeaderNew'] = '';
        }
        if ($withoutHeaderFooter && !array_key_exists('FooterNew', $vars)) {
            $vars['FooterNew'] = '';
        }

        return self::viewFetch($template, array(
            'path' => CFG_BASE_TPL_ROOT,
            'vars' => $vars
        ));
    }

    public static function viewFetch($template, $parameters = array())
    {
        $view = new View();
        $result = $view->fetch($template, $parameters);
        unset($view);

        return $result;
    }

    public static function isSellFree($lang = null)
    {
        $lang = ($lang) ? $lang : Session::getActiveLang();
        return InstanceProvider::getObject()->isSellFree($lang);
    }

    public static function  compileCustomScss()
    {
        try {
            require_once CFG_LIB_ROOT . "/vendor/scssphp/scss.inc.php";
            require_once CFG_LIB_ROOT . "/vendor/scss-compass/compass.inc.php";

            $themeDir = General::getThemeDir();
            $currentTheme = self::getCurrentTheme();

            $scss = new scssc();
            new scss_compass($scss);
            $scss->addImportPath($themeDir . '/css/sass/'); // основной файл стилей screen.scss
            $scss->addImportPath(CFG_APP_ROOT . '/lib/vendor/scss-compass/stylesheets/'); // дополнительные библиотеки
            $scss->setFormatter('scss_formatter');

            $variablesCustom = self::generateSassVariablesCustom();
            $customScss = self::getConfigValue('custom_scss_after_scss_'.$currentTheme);
            $css = $scss->compile('
                @import "compass.scss";
                @import "variables.default.scss";
                ' . $variablesCustom . '
                @import "screen.scss";
                ' . $customScss . '
            ');

            if (file_put_contents($themeDir . '/css/screen-custom.css', $css) === false) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
    
    private static function generateSassVariablesCustom()
    {
        $variablesCustom = '';
        $currentTheme = self::getCurrentTheme();

        // background
        if (self::getConfigValue('custom_scss_body_background_color_'.$currentTheme)) {
            $variablesCustom .= '$bodyBackgroundColor: ' . self::getConfigValue('custom_scss_body_background_color_'.$currentTheme) . ';';
        }
        if (self::getConfigValue('custom_scss_body_background_image_'.$currentTheme)) {
            $variablesCustom .= '$bodyBackgroundImage: url("' . self::getConfigValue('custom_scss_body_background_image_'.$currentTheme) . '");';
        }
        if (self::getConfigValue('custom_scss_background_repeat_'.$currentTheme)) {
            $variablesCustom .= '$bodyBackgroundRepeat: ' . self::getConfigValue('custom_scss_background_repeat_'.$currentTheme) . ';';
        }
        if (self::getConfigValue('custom_scss_background_position_'.$currentTheme)) {
            $variablesCustom .= '$bodyBackgroundPosition: ' . self::getConfigValue('custom_scss_background_position_'.$currentTheme) . ' 0px;';
        }
        if (self::getConfigValue('custom_scss_background_attachment_'.$currentTheme)) {
            $variablesCustom .= '$bodyBackgroundAttachment: ' . self::getConfigValue('custom_scss_background_attachment_'.$currentTheme) . ';';
        }

        // design
        if (self::getConfigValue('custom_scss_design_color_1_'.$currentTheme)) {
            $variablesCustom .= '$designColor1: ' . self::getConfigValue('custom_scss_design_color_1_'.$currentTheme) . ';';
        }
        if (self::getConfigValue('custom_scss_design_color_2_'.$currentTheme)) {
            $variablesCustom .= '$designColor2: ' . self::getConfigValue('custom_scss_design_color_2_'.$currentTheme) . ';';
        }
        if (self::getConfigValue('custom_scss_body_text_color_'.$currentTheme)) {
            $variablesCustom .= '$bodyTextColor: ' . self::getConfigValue('custom_scss_body_text_color_'.$currentTheme) . ';';
        }
        if (self::getConfigValue('custom_scss_headers_text_color_'.$currentTheme)) {
            $variablesCustom .= '$headersTextColor: ' . self::getConfigValue('custom_scss_headers_text_color_'.$currentTheme) . ';';
        }
        if (self::getConfigValue('custom_scss_border_color_'.$currentTheme)) {
            $variablesCustom .= '$borderColor: ' . self::getConfigValue('custom_scss_border_color_'.$currentTheme) . ';';
        }
        if (self::getConfigValue('custom_scss_success_color_'.$currentTheme)) {
            $variablesCustom .= '$successColor: ' . self::getConfigValue('custom_scss_success_color_'.$currentTheme) . ';';
        }
        if (self::getConfigValue('custom_scss_info_color_'.$currentTheme)) {
            $variablesCustom .= '$infoColor: ' . self::getConfigValue('custom_scss_info_color_'.$currentTheme) . ';';
        }
        if (self::getConfigValue('custom_scss_warning_color_'.$currentTheme)) {
            $variablesCustom .= '$warningColor: ' . self::getConfigValue('custom_scss_warning_color_'.$currentTheme) . ';';
        }
        if (self::getConfigValue('custom_scss_danger_color_'.$currentTheme)) {
            $variablesCustom .= '$dangerColor: ' . self::getConfigValue('custom_scss_danger_color_'.$currentTheme) . ';';
        }

        return $variablesCustom;
    }

    /**
     * Проверяет наличие маршрута в конфиге ролей
     * /config/routingRules (только для новой платформы!!!)
     *
     * @param string $route - маршрут
     * @return bool
     */
    public static function onNewPlatformScript($route)
    {
        if (!self::getConfigValue('is_old_platform')) {
            $scriptNamesStack = Router::getInstance()->getRulesDeclarations();
            return array_key_exists($route, $scriptNamesStack);
        }

        return false;
    }

    public static function getSearchParams()
    {
        $vid = false;
        $brand = false;
        $cid = false;
        $search = false;

        if (RequestWrapper::getValueSafe('vid')) {
            $vid = RequestWrapper::getValueSafe('vid');
        }  elseif (RequestWrapper::getValueSafe('id') && self::$scriptName == 'vendor') {
            $vid = RequestWrapper::getValueSafe('id');
        } elseif (RequestWrapper::getValueSafe('id') && SCRIPT_NAME == 'vendor') {
            $vid = RequestWrapper::getValueSafe('id');
        }
        if (RequestWrapper::getValueSafe('brand')) {
            $brand = RequestWrapper::getValueSafe('brand');
        } elseif (RequestWrapper::getValueSafe('id') && self::$scriptName == 'brand') {
            $brand = RequestWrapper::getValueSafe('id');
        } elseif (RequestWrapper::getValueSafe('id') && SCRIPT_NAME == 'brand') {
            $brand = RequestWrapper::getValueSafe('id');
        }
        if (RequestWrapper::getValueSafe('cid')) {
            $cid = RequestWrapper::getValueSafe('cid');
        }
        if (RequestWrapper::getValueSafe('search')) {
            $search = RequestWrapper::getValueSafe('search');
        }

        return array(
            'vid' => $vid,
            'brand' => $brand,
            'cid' => $cid,
            'search' => $search,
        );
    }

    public static function isShowSearchBar()
    {
        $isLimitItemsByCatalog = InstanceProvider::getObject()->isLimitItemsByCatalog();

        if ($isLimitItemsByCatalog) {
            $cid = RequestWrapper::get('cid');
            if (
                RequestWrapper::get('p') == 'subcategory' ||
                RequestWrapper::get('p') == 'category' ||
                (RequestWrapper::get('p') == 'search' && !empty($cid))
            ) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public static function setGroupId($id) {
        self::$statisticGroupId = $id;
    }

    public static function getGroupId() {
        return self::$statisticGroupId;
    }

    public static function registerOtapiRequest($requestId, $time, $method = null)
    {
        array_push(self::$otapiRequests, array('requestId' => $requestId, 'time' => ceil($time), 'method' => $method));
    }

    public static function storeRequestGroup()
    {
        $logs = Debugger::getAllByType(Debugger::LOG_OTAPILIB_TYPE);
        $requestsFullTime = Debugger::calculateTime($logs);

        $params = array(
            'GroupId'   => self::getGroupId(),
            'FullTime'  => ceil((microtime(true) - $GLOBALS['script_start_time']) * 1000),
            'WorkTime'  => ceil($requestsFullTime * 1000),
            'Requests'  => self::$otapiRequests
        );

        $xmlParams = new SimpleXMLElement('<RequestGroupInfo></RequestGroupInfo>');
        $xmlParams->addChild('GroupId', $params['GroupId']);
        $xmlParams->addChild('FullTime', $params['FullTime']);
        $xmlParams->addChild('WorkTime', $params['WorkTime']);

        $el = $xmlParams->addChild('Requests');
        foreach ($params['Requests'] as $req) {
            $child = $el->addChild('Request');
            $child->addAttribute('Id', $req['requestId']);
            $child->addAttribute('Time', $req['time']);
        }
        $xmlParams = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());

        try {
            OTAPILib2::StoreRequestGroup(Session::getActiveLang(), $xmlParams, $response);
            OTAPILib2::makeRequests();
        } catch (Exception $e) {
        }
    }

    public static function getDesignThemes()
    {
        $themesDir = glob(CFG_APP_ROOT . '/themes/*');
        $currentThemeDir = General::getThemeDir();
        $currentTheme = basename($currentThemeDir);

        $themes = [];

        // при отсутствии директории с текущей темой, добавить тему вручную
        if (!in_array($currentThemeDir, $themesDir)) {
            $themes[] = [
                'name' => $currentTheme,
                'disabled' => false,
                'active' => true
            ];
        }

        foreach ($themesDir as $themeDir) {
            $name = basename($themeDir);

            $themes[] = [
                'name' => $name,
                'disabled' => false,
                'active' => $name == $currentTheme ? true : false
            ];
        }

        return $themes;
    }

    public static function hasDesignThemesCustom()
    {
        $otThemes = ['lite', 'elastic', 'fashi'];

        foreach (self::getDesignThemes() as $theme) {
            if (!in_array($theme['name'], $otThemes)) {
                return true;
            }
        }

        return false;
    }

    public static function hasDesignCustom()
    {
        $hasDesignThemesCustom = self::hasDesignThemesCustom();
        $customFilesDirectory = self::getThemeDir() . '/viewscustom/';
        $customFiles = glob($customFilesDirectory . '*');

        return ($hasDesignThemesCustom || count($customFiles) > 0);
    }

    public static function getCurrentTheme()
    {
        return self::getConfigValue('design_theme', self::$baseTheme);
    }

    public static function getThemeDir()
    {
        return CFG_APP_ROOT . self::getThemeWebDir();
    }

    public static function getThemeWebDir()
    {
        $currentTheme = self::getCurrentTheme();
        return '/themes/' . $currentTheme;
    }

    public static function downloadFile($url, $path)
    {
        $file = @fopen($url, "rb");
        if ($file) {
            $newFile = @fopen($path, "wb");

            if ($newFile) {
                while (!feof($file))
                    fwrite($newFile, fread($file, 1024 * 8), 1024 * 8);
            } else {
                throw new Exception(LangAdmin::get('Update_folder_not_writable_now'));
            }
        } else {
            throw new Exception(LangAdmin::get('There_is_no_archive_of_updates'));
        }

        if ($file)
            fclose($file);

        if ($newFile)
            fclose($newFile);
    }
}
