<?php

class Plugins
{
    const NOTHING_CALLED = false;

    /**
     * @var Array
     */
    private static $registry;

    public static function onAddScriptProcessor($scriptName, $suffix = '')
    {
        if (!file_exists(CFG_APP_ROOT . '/config/script_controller' . $suffix . '.xml')) {
            return false;
        }
        $xml = simplexml_load_file(CFG_APP_ROOT . '/config/script_controller' . $suffix . '.xml');
        $script = $xml->xpath('script[@name="' . htmlspecialchars($scriptName) . '"]');

        if (@!$script[0])
            $script = self::findPackagesRoutes($scriptName);
        if (@!$script[0])
            return false;

        $script = $script[0];

        $action = $script->xpath('action');
        if (count($action)) {
            $action = $action[0];
            $blockName = (string)$action['block'];
            $methodName = (string)$action['action'] . 'Action';

            if (isset($action['path']) && !empty($action['path'])) {
                require_once CFG_APP_ROOT . '/' . $action['path'] . $blockName . '.class.php';
            }
            $block = new $blockName;

            $parameters = array();
            if (count($action->xpath('param'))) {
                foreach ($action->xpath('param') as $p) {
                    $parameters[(string)$p['name']] =
                        call_user_func(array('RequestWrapper', (string)$p['method']), (string)$p['name']);
                }
            }

            $parameters[] = new RequestWrapper();
            call_user_func_array(array($block, $methodName), $parameters);
            die();
        }

        $template = $script->xpath('template');

        define('CFG_PAGE_TEMPLATE', (string)$template[0]['name']);
        if (isset($script['no_debug']) && !defined('NO_DEBUG'))
            define('NO_DEBUG', true);

        $templateBlocks = array();
        $blocks = $script->xpath('blocks/block');
        foreach ($blocks as $block) {
            if (isset($block['path']) && !empty($block['path'])) {
                require_once CFG_APP_ROOT . '/packages/' . $block['path'] . '/' . (string)$block['name'] . '.class.php';
            }
            $templateBlocks[] = (string)$block['name'];
        }
        return $templateBlocks;
    }

    public static function onAddScriptProcessorCheck($scriptName, $suffix = '')
    {
        if (file_exists(CFG_APP_ROOT . '/config/script_controller' . $suffix . '.xml')) {
            $xml = simplexml_load_file(CFG_APP_ROOT . '/config/script_controller' . $suffix . '.xml');
            $script = $xml->xpath('script[@name="' . htmlspecialchars($scriptName) . '"]');
            if (@!$script[0])
                $script = self::findPackagesRoutes($scriptName);
            if (@!$script[0])
                return false;
            return $scriptName;
        }
        return false;
    }

    public static function onRenderUserEditForm(&$user)
    {
        if (defined('CFG_TAO141')) {
            return Tao141Clients::onRenderUserEditForm($user);
        }
    }

    public static function onAddUser($data, $newUserId)
    {
        if (defined('CFG_TAO141')) {
            return Tao141Clients::onAddUser($data, $newUserId);
        }
        if (defined('CFG_SITE_CUSTOMIZE') && CFG_SITE_CUSTOMIZE == 'Tbe') {
            return TbeClients::onAddUser($data);
        }
    }

    public static function onEditUser($data)
    {
        if (defined('CFG_TAO141')) {
            return Tao141Clients::onEditUser($data);
        }
        if (defined('CFG_SITE_CUSTOMIZE') && CFG_SITE_CUSTOMIZE == 'Tbe') {
            return TbeClients::onEditUser($data);
        }
    }

    public static function onRenderMoneyInfo($sid)
    {
        if (defined('CFG_TAO141')) {
            return Tao141Clients::onRenderMoneyInfo($sid);
        } else {
            return false;
        }
    }

    public static function onCreateOrder($sid, $model)
    {
        if (defined('CFG_TAO141')) {
            return Tao141Clients::onCreateOrder($sid, $model);
        } else {
            return 0;
        }
    }

    public static function onRenderFilterOrdersForm()
    {
        if (defined('CFG_SITE_CUSTOMIZE')) {
            $res = @call_user_func_array(CFG_SITE_CUSTOMIZE . 'Clients::onRenderFilterOrdersForm', array());
            if ($res) return $res;
        }
        return false;
    }

    public static function onRenderNotificationForm()
    {
        if (defined('SEND_EMAIL_NOTIFICATION')) {
            ob_start();
            require dirname(__FILE__) . '/tpl/clients.onRenderNotificationForm.html';
            $c = ob_get_contents();
            ob_end_clean();
            return $c;
        }
        return false;
    }

    public static function invokeEvent($event, $args = array())
    {
        if (defined('CFG_SITE_CUSTOMIZE')) {
            $res = @call_user_func_array(CFG_SITE_CUSTOMIZE . 'Clients::' . $event, $args);
            if ($res)
                return $res;
        }

        $result = '';

        list($isFinal, $invokePackage) = self::findPackageForEvent($event, $args);
        if ($invokePackage != self::NOTHING_CALLED && $isFinal)
            return $invokePackage;
        elseif ($invokePackage != self::NOTHING_CALLED) {
            is_array($invokePackage) ? $result = $invokePackage : $result .= $invokePackage;
        }
        $invokePackage = self::callPriorityPackage($event, $args);
        if ($invokePackage != self::NOTHING_CALLED) {
            if (is_array($invokePackage))
                $result = array_merge((array)$result, (array)$invokePackage);
            else
                $result .= $invokePackage;
        }
        return $result === '' ? false : $result;
    }

    private static function callPriorityPackage($event, $args)
    {

        if (!file_exists(CFG_APP_ROOT . '/config/events.xml'))
            return self::NOTHING_CALLED;

        $events = simplexml_load_file(CFG_APP_ROOT . '/config/events.xml');
        $eventHandler = $events->xpath('event[@name="' . $event . '"]');

        if (!$eventHandler)
            return self::NOTHING_CALLED;

        if (!class_exists((string)$eventHandler[0]['class_name'], false)) {
            require CFG_APP_ROOT . '/packages/' . (string)$eventHandler[0]['pakage_path'] . '/'
                . (string)$eventHandler[0]['class_name'] . '.class.php';
        }

        return call_user_func_array((string)$eventHandler[0]['class_name'] . '::' . $event, $args);
    }

    private static function findPackageForEvent($event, $args)
    {
        $packages = glob(CFG_APP_ROOT . '/packages/*');
        if (!$packages)
            return array(false, self::NOTHING_CALLED);

        $result = '';

        foreach ($packages as $package) {
            if (is_dir($package) && file_exists($package . '/config/events.xml')) {
                $events = simplexml_load_file($package . '/config/events.xml');
                $eventHandler = $events->xpath('event[@name="' . $event . '"]');
                if (!$eventHandler)
                    continue;

                if (!class_exists((string)$eventHandler[0]['class_name'], false)) {
                    require CFG_APP_ROOT . '/packages/' . (string)$eventHandler[0]['pakage_path'] . '/'
                        . (string)$eventHandler[0]['class_name'] . '.class.php';
                }
                if (isset($eventHandler[0]['allow_other_handlers']) && (string)$eventHandler[0]['allow_other_handlers']) {
                    $plugin_result = call_user_func_array((string)$eventHandler[0]['class_name'] . '::' . $event, $args);
                    if (is_array($plugin_result)) {
                        $result[] = $plugin_result;
                    } else {
                        $result .= $plugin_result;
                    }
                } else {
                    return array(true, call_user_func_array((string)$eventHandler[0]['class_name'] . '::' . $event, $args));
                }
            }
        }
        return array(false, $result);
    }

    private static function findPackagesRoutes($scriptName)
    {
        $packages = glob(CFG_APP_ROOT . '/packages/*');
        if (!$packages)
            return false;

        foreach ($packages as $package) {
            if (is_dir($package) && file_exists($package . '/config/script_controller.xml')) {
                $routes = simplexml_load_file($package . '/config/script_controller.xml');
                $routeHandler = $routes->xpath('script[@name="' . htmlspecialchars($scriptName) . '"]');
                if (@$routeHandler[0]) {
                    return $routeHandler;
                }
            }
        }

        return false;
    }

    public static function runEvent($event, $args = array())
    {
        $events = self::getEventsList();

        $result = '';

        if (isset($events[$event])) {
            foreach ($events[$event] as $plugin) {
                $pluginClass = $plugin['name'] . 'Plugin';
                if (!class_exists($pluginClass, false)) {
                    require $plugin['path'] . '/' . $pluginClass . '.class.php';
                }

                if (method_exists($pluginClass, $event)) {
                    $class = new $pluginClass();
                    $pluginResult = $class->$event($args);
                    $result .= $pluginResult;
                }
            }

        }

        return $result;
    }

    public static function runSerialEvent($event, $args = array())
    {
        $events = self::getEventsList();

        if (isset($events[$event])) {
            foreach ($events[$event] as $plugin) {
                $pluginClass = $plugin['name'] . 'Plugin';
                if (!class_exists($pluginClass, false)) {
                    require $plugin['path'] . '/' . $pluginClass . '.class.php';
                }

                if (method_exists($pluginClass, $event)) {
                    $class = new $pluginClass();
                    $args = $class->$event($args);
                }
            }

        }

        return $args;
    }

    /*
     * Получить массив вида array('nameEvent' => array(0 => 'plugin1', 1 => 'plugin2'))
     * 
     * @return array
     */
    private static function getEventsList()
    {
        $cacheKey = 'PluginEventsList:Result';
        if (isset(self::$registry[$cacheKey])) {
            return self::$registry[$cacheKey];
        }

        $cacher = new FileAndMysqlMemoryCache(new CMS());
        if ($cacher->Exists($cacheKey)) {
            $result = unserialize($cacher->GetCacheEl($cacheKey));
        } else {
            $result = array();

            $packages = glob(CFG_APP_ROOT . '/packages/*');

            foreach ($packages as $package) {
                if (is_dir($package) && file_exists($package . '/config/config.xml')) {
                    $config = simplexml_load_file($package . '/config/config.xml');

                    foreach ($config->events->event as $event) {
                        $result[(string)$event['name']][] = array(
                            'name' => basename($package),
                            'path' => $package,
                            'priority' => (isset($event['priority'])) ? (int)$event['priority'] : 0
                        );
                    }
                }
            }
            // TODO: добавить сортировку по priority

            $cacher->AddCacheEl($cacheKey, 86400, serialize($result));
        }

        self::$registry[$cacheKey] = $result;
        return $result;
    }

    public static function getPluginsInfo($lang)
    {
        $plugins = array();

        $C = new Curl(CFG_TOOLS_URL . '/plugins/');
        $C->setPost(array('request' => self::generateXmlForTools($lang)), false);
        $C->setReferer($_SERVER['SERVER_NAME']);
        $C->connect();
        $xml = $C->getWebPage();
        $result = simplexml_load_string($xml);

        if (
            $result !== false &&
            isset($result->ErrorCode) &&
            (string)$result->ErrorCode == 'Ok'
        ) {
            foreach ($result->Result->Plugins->Plugin as $value) {
                $plugins[] = array(
                    'name' => (string)$value->Name,
                    'title' => (string)$value->Title,
                    'description' => (string)$value->Description,
                    'version' => (string)$value->Version,
                    'boxVersion' => (string)$value->BoxVersion,
                    'isAllowedForCurrentBoxVersion' => ((string)$value->IsAllowedForCurrentBoxVersion == 'true') ? true : false,
                    'installed' => ((string)$value->Installed == 'true') ? true : false,
                    'installedVersion' => (string)$value->InstalledVersion,
                    'canBeInstall' => ((string)$value->CanBeInstall == 'true') ? true : false,
                    'canBeUpdate' => ((string)$value->CanBeUpdate == 'true') ? true : false,
                    'canBeDelete' => ((string)$value->CanBeDelete == 'true') ? true : false,
                    'autoUpdate' => ((string)$value->AutoUpdate == 'true') ? true : false,
                    'downloadUrl' => (string)$value->DownloadUrl,
                );
            }
        }

        return $plugins;
    }

    private static function generateXmlForTools($lang)
    {
        $xml = new SimpleXMLElement('<Body/>');
        $request = $xml->addChild('Request');

        $request->addChild('BoxVersion', OTBase::getVersion());
        $request->addChild('PhpVersion', phpversion());
        $request->addChild('Language', Session::getActiveAdminLang());

        $features = $request->addChild('Features');
        foreach (General::$enabledFeatures as $value) {
            $features->addChild('Feature', $value);
        }

        $boxSettings = $request->addChild('BoxSettings');
        foreach (General::$siteConf as $key => $value) {
            $setting = $boxSettings->addChild('Setting', htmlspecialchars((string)$value));
            $setting->addAttribute('Name', $key);
        }

        $plugins = $request->addChild('InstalledPlugins');
        foreach (self::getAllPlugins($lang) as $value) {
            $plugin = $plugins->addChild('Plugin');
            $plugin->addChild('Name', $value['name']);
            $plugin->addChild('Version', (string)$value['version']);
            $plugin->addChild('Status', $value['status'] ? 'true' : 'false');
        }

        return $xml->asXML();
    }

    public static function getAllPlugins($lang)
    {
        $plugins = array();

        // получаем список активных плагинов
        $active = glob(CFG_APP_ROOT . '/packages/*');

        foreach ($active as $path) {
            // пропускаем файл 'index.html'
            if (substr($path, strlen($path) - strlen('index.html')) == 'index.html') {
                continue;
            }

            try {
                $name = basename($path);
                $plugin = self::getPluginInfo($name, $lang);
                $plugins[] = $plugin;
            } catch (Exception $e) {
            }
        }

        // получаем список не активных плагинов
        $inactive = glob(CFG_APP_ROOT . '/packages/inactive/*');

        foreach ($inactive as $path) {
            try {
                $name = basename($path);
                $plugin = self::getPluginInfo($name, $lang);
                $plugins[] = $plugin;
            } catch (Exception $e) {
            }
        }

        return $plugins;
    }

    // получить информацию о пагине
    public static function getPluginInfo($pluginName, $lang)
    {
        // проверяем наличие плагина в активных/не активных
        if (file_exists(CFG_APP_ROOT . '/packages/' . $pluginName . '/config/config.xml')) {
            $status = true;
            $path = CFG_APP_ROOT . '/packages/' . $pluginName . '/';
        } elseif (file_exists(CFG_APP_ROOT . '/packages/inactive/' . $pluginName . '/config/config.xml')) {
            $status = false;
            $path = CFG_APP_ROOT . '/packages/inactive/' . $pluginName . '/';
        } else {
            throw new Exception('Plugin "'.$pluginName.'" not found');
        }

        $plugin = array(
            'name' => $pluginName,
            'title' => $pluginName,
            'path' => $path,
            'status' => $status,
            'version' => ''
        );

        $config = simplexml_load_file($path . '/config/config.xml');

        if (isset($config->title) && $config->xpath('//plugin/title[@lang="' . $lang . '"]')) {
            $title = $config->xpath('//plugin/title[@lang="' . $lang . '"]');
            $plugin['title'] = (string)$title[0];
        } elseif (isset($config->title[0])) {
            $plugin['title'] = (string)$config->title[0];
        }
        if (isset($config->version[0])) {
            $plugin['version'] = (string)$config->version[0];
        }

        return $plugin;
    }

    public static function updatePlugin($name, $downloadUrl, $lang)
    {
        $plugin = self::getPluginInfo($name, $lang);

        // скачиваем плагин
        $archiveFile = CFG_APP_ROOT . '/updates/' . $name . '.zip';
        General::downloadFile($downloadUrl, $archiveFile);

        // распаковываем плагин поверх текущего
        self::extractPlugin($archiveFile, $plugin['path']);
        unlink($archiveFile);

        self::clearCache();
        $plugin = self::getPluginInfo($name, $lang);

        // после обновления пытаемся вызвать метод плагина updatePlugin
        self::callPluginMethod($plugin, 'updatePlugin');
    }

    public static function clearCache()
    {
        $CMS = new CMS();
        $cacher = new FileAndMysqlMemoryCache($CMS);
        try {
            $cacher->DelCacheEl('PluginEventsList:Result'); // кеш для списка плагинов
            $cacher->DelCacheEl('General:xmlConfigController'); // кеш для роутинга
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public static function extractPlugin($pathToArchive, $pathToExtract)
    {
        $zip = new ZipArchive();
        if ($zip->open($pathToArchive) === true) {
            $zip->extractTo($pathToExtract);
            $zip->close();

            return true;
        }

        throw new Exception(LangAdmin::get('There_is_no_archive_of_updates'));
    }

    public static function callPluginMethod(array $plugin, $nameMethod)
    {
        $className = $plugin['name'] . 'Plugin';
        if (! class_exists($className, false)) {
            require $plugin['path'] . "/" . $className . ".class.php";
        }

        if (method_exists($className, $nameMethod)) {
            $objPlugin = new $className();
            return $objPlugin->$nameMethod();
        }

        return false;
    }
}
