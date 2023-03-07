<?php

class PluginsUtil extends GeneralUtil
{
    protected $defaulAction = 'list';

    private function clearCache()
    {
        return Plugins::clearCache();
    }

    /**
     * @param RequestWrapper $request
     */
    public function listAction($request)
    {
        $plugins = Plugins::getAllPlugins(Session::getActiveAdminLang());
        $this->tpl->assign('plugins', $plugins);

        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function activateAction($request)
    {
        $name = $request->getValue('name');

        if (
            file_exists(CFG_APP_ROOT . '/packages/inactive/' . $name)
            && is_dir(CFG_APP_ROOT . '/packages/inactive/' . $name)
        ) {
            $this->activatePlugin($name);
        }

        $this->redirect($this->getPageUrl()->generate(array('cmd' => 'pluginsutil', 'do' => 'default')));
    }

    private function activatePlugin($name)
    {
        rename(CFG_APP_ROOT . '/packages/inactive/' . $name, CFG_APP_ROOT . '/packages/' . $name);
        $this->clearCache();

        $plugin = $this->getPluginInfo($name);
        // после активации пытаемся вызвать метод плагина activatePlugin
        $this->callPluginMethod($plugin, 'activatePlugin');
    }

    /**
     * @param RequestWrapper $request
     */
    public function deactivateAction($request)
    {
        $name = $request->getValue('name');

        if (
            file_exists(CFG_APP_ROOT . '/packages/' . $name)
            && is_dir(CFG_APP_ROOT . '/packages/' . $name)
        ) {

            $this->deactivatePlugin($name);
        }

        $this->redirect($this->getPageUrl()->generate(array('cmd' => 'pluginsutil', 'do' => 'default')));
    }

    private function deactivatePlugin($name)
    {
        rename(CFG_APP_ROOT . '/packages/' . $name, CFG_APP_ROOT . '/packages/inactive/' . $name);
        $this->clearCache();

        $plugin = $this->getPluginInfo($name);
        // после выключения пытаемся вызвать метод плагина deactivatePlugin
        $this->callPluginMethod($plugin, 'deactivatePlugin');
    }

    /**
     * @param RequestWrapper $request
     */
    public function viewAction($request)
    {
        $name = $request->request('plugin');

        try {
            $plugin = $this->getPluginInfo($name);

            $className = $name . 'Plugin';
            if (!class_exists($className, false)) {
                require $plugin['path'] . "/" . $className . ".class.php";
            }

            if (method_exists($className, 'renderPluginPage')) {
                $objPlugin = new $className();
                $result = $objPlugin->renderPluginPage($request);
            } else {
                $result = LangAdmin::get('Method_renderPluginPage_not_defined');
            }

            $this->tpl->assign('plugin', $plugin);
            $this->tpl->assign('result', $result);
        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }

        print $this->fetchTemplate();
    }

    // получить информацию о пагине
    private function getPluginInfo($pluginName)
    {
        return Plugins::getPluginInfo($pluginName, Session::getActiveAdminLang());
    }

    /**
     * @param RequestWrapper $request
     */
    public function controlAction($request)
    {
        $pluginsInfo = Plugins::getPluginsInfo(Session::getActiveAdminLang());
        $this->tpl->assign('pluginsInfo', $pluginsInfo);
        $this->tpl->assign('boxVersion', OTBase::getVersion());

        print $this->fetchTemplate();
    }

    public function installPluginAction($request)
    {
        try {
            $name = $request->getValue('name');

            // если плагин уже установлен - ошибка
            try {
                $plugin = $this->getPluginInfo($name);
                $this->respondAjaxError(LangAdmin::get('Plugin_already_installed'));
            } catch (Exception $e) {
            }

            // скачиваем плагин
            $downloadUrl = $request->getValue('downloadUrl');
            $archiveFile = CFG_APP_ROOT . '/updates/' . $name . '.zip';
            General::downloadFile($downloadUrl, $archiveFile);

            // распаковываем плагин
            Plugins::extractPlugin($archiveFile, CFG_APP_ROOT . '/packages/inactive/' . $name . '/');
            unlink($archiveFile);

            $plugin = $this->getPluginInfo($name);
            // после установки пытаемся вызвать метод плагина installPlugin
            $this->callPluginMethod($plugin, 'installPlugin');

            // активируем плагин
            $this->activatePlugin($name);

            $this->clearCache();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse();
    }

    public function updatePluginAction($request)
    {
        try {
            $name = $request->getValue('name');
            $downloadUrl = $request->getValue('downloadUrl');
            $lang = Session::getActiveAdminLang();
            Plugins::updatePlugin($name, $downloadUrl, $lang);
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse();
    }

    public function deletePluginAction($request)
    {
        try {
            $name = $request->getValue('name');

            $plugin = $this->getPluginInfo($name);
            // перед удалением пытаемся вызвать метод плагина deletePlugin
            $this->callPluginMethod($plugin, 'deletePlugin');

            General::rrmdir($plugin['path']);
            $this->clearCache();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse();
    }

    private function callPluginMethod($plugin, $nameMethod)
    {
        return Plugins::callPluginMethod($plugin, $nameMethod);
    }

    public function requestAdminAction($request)
    {
        try {
            $plugin = $this->getPluginInfo($request->getValue('pluginName'));
            $this->callPluginMethod($plugin, 'requestAdmin');
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse();
    }

    /**
     * @param RequestWrapper $request
     */
    public function saveSiteConfigAction($request)
    {
        $config = new SiteConfigurationRepository($this->cms);
        $config->SetActiveLang(Session::get('active_lang_pluginsutil'));

        $config->Set($request->getValue('name'), $request->getValue('value'));

        $this->sendAjaxResponse(array(
            'result' => 'ok',
        ));
    }
}
