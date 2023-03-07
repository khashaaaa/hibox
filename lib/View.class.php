<?php

class View
{
    private $vars = array();

    public function assign($name, $value)
    {
        $this->vars[$name] = $value;
    }

    public function clear($name)
    {
        if (isset($this->vars[$name])) {
            unset($this->vars[$name]);
        }
    }

    public function fetch($template, $parameters = array())
    {
        // assign vars
        if (isset($parameters['vars'])) {
            foreach ($parameters['vars'] as $key => $value) {
                $this->assign($key, $value);
            }
        }

        // проверяем использование кеша
        if (isset($parameters['cache']) && $parameters['cache']) {
            $cacher = new FileAndMysqlMemoryCache(new CMS());

            $cacheId = 'View:' . $template;
            $cacheId = (isset($parameters['cacheHash'])) ? $cacheId . $parameters['cacheHash'] : $cacheId;
        }

        // проверяем кеш
        if (isset($cacher) && isset($cacheId) && $cacher->Exists($cacheId)) {
            $result = $cacher->GetCacheEl($cacheId);
        } else {
            if (sizeof($this->vars) > 0) {
                extract($this->vars);
            }

            $path = (isset($parameters['path'])) ? $parameters['path'] : General::getThemeDir() . '/views';
            $file = $this->getFilePath($path, $template . '.html');

            if (!file_exists($file) && isset(General::$params['extendsTheme'])) {
                // если шаблон есть в наследуемой теме, берем его
                $path = str_replace('/' . General::getCurrentTheme() . '/', '/' . General::$params['extendsTheme'] . '/', General::getThemeDir() . '/views');
                $baseFile = $this->getFilePath($path, $template . '.html');
                if (file_exists($baseFile)) {
                    $file = $baseFile;
                }
            }

            if (file_exists($file)) {
                // рендерим шаблон
                ob_start();

                include $file;

                $result = ob_get_clean();
            } else {
                $result = 'Template file "' . $file . '" does not exists.';
            }

            // проверяем необходимость записи в кеш
            if (isset($cacher) && isset($cacheId)) {
                $cacheTime = isset($parameters['cache_time']) ? $parameters['cache_time'] : 3600;
                $cacher->AddCacheEl($cacheId, $cacheTime, $result);
            }
        }

        $before = Plugins::runEvent('onBeforeRenderView_' . str_replace(array('\\', '/', '-'), '_', $template), $this->vars);
        $after = Plugins::runEvent('onAfterRenderView_' . str_replace(array('\\', '/', '-'), '_', $template), $this->vars);

        $result = $before . $result . $after;

        return $result;
    }

    public function display($template)
    {
        print $this->fetch($template);
    }

    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES);
    }
    
    public static function getFilePath($path, $file)
    {
        $filePath = $path . '/' . $file;
        if (file_exists($path . 'custom/' . $file) && General::getConfigValue('use_custom_view', 1)) {
            $filePath = $path . 'custom/' . $file;
        }

        return $filePath;
    }

    // TODO: устаревший метод - удалить после того как перестанет использоваться
    /**
     * @param $templateName
     * @param $templateFileName
     * @param $templatePath
     * @param array $parameters
     * @return string
     */
    public static function fetchTemplate($templateName, $templateFileName, $templatePath, $parameters = array()){
        $HSTemplate_options = array(
            'template_path' => CFG_BASE_TPL_ROOT,
            'cache_path'    => CFG_APP_ROOT . '/cache',
            'debug'         => false,
        );
        $HSTemplate = new HSTemplate($HSTemplate_options);
        $templateDisplay = $HSTemplate->getDisplay($templateName, true);

        foreach($parameters as $key => $value) {
            $templateDisplay->assign($key, $value);
        }

        $tpl = CFG_TPL_ROOT . $templatePath;
        if (! file_exists($tpl . $templateFileName . '.html')) {
            $tpl = CFG_BASE_TPL_ROOT . $templatePath;
        }

        $templateDisplay->addTemplate($templateName, $templateFileName.'.html', $tpl);
        return $templateDisplay->fetch($templateName, false, true);
    }
    
    public function getUser()
    {
        return User::getObject();
    }
}
