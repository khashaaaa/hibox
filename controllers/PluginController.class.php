<?php

class PluginController extends GeneralContoller
{
    public function requestAction($pluginName)
    {
        $request = new RequestWrapper();
        try {
            $className = $pluginName . 'Plugin';
            if (!file_exists(CFG_APP_ROOT . '/packages/' . $pluginName . '/config/config.xml')) {
                throw new Exception('Plugin "'.$pluginName.'" not found');
            }

            if (!class_exists($className, false)) {
                require CFG_APP_ROOT . '/packages/' . $pluginName . '/' . $className . ".class.php";
            }

            if (method_exists($className, 'request')) {
                $objPlugin = new $className();
                return $objPlugin->request($request);
            }
        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }
    }
}
