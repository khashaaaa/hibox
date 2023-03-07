<?php

class AssetsMin
{
    private static $defaultScripts = array();
    private static $scripts = array();
    private static $styles = array();
    private static $scriptsСhunks = array();

    private static $libPuth = '/lib/vendor/min/?f=';
    private static $libPuthGroups = '/lib/vendor/min/?g=';

    private static $assetsGroups = [];


    private static function pullInStack(&$stack, $source)
    {
        if (! in_array($source, $stack)) {
            $stack[] = $source;
        }
    }

    public static function registerJsFile($url, $options = array())
    {
        if (RequestWrapper::isAjax()) {
            if (!in_array($url, self::$scripts)) {
                echo '<script type="text/javascript" src="' . $url . '?' . OTBase::getVersion() . '"></script>'; // xhr запросы не помещаются в стек для вывода, а сразу выводятся
                self::pullInStack(self::$scripts, $url);
            }
        } else {
            if (isset($options['minify']) && $options['minify'] == false) {
                self::pullInStack(self::$defaultScripts, $url);
            } else {
                self::pullInStack(self::$scripts, $url);
            }
        }
    }


    public static function registerCssFile($url)
    {
        if (RequestWrapper::isAjax()) {
            if (!in_array($url, self::$styles)) {
                echo '<link rel="stylesheet" href="' . $url . '?' . OTBase::getVersion() . '" />';
                self::pullInStack(self::$styles, $url);
            }
        } else {
            self::pullInStack(self::$styles, $url);
        }
    }


    public static function registerJs($string)
    {
        if (RequestWrapper::isAjax())
            echo $string; // xhr запросы не помещаются в стек для вывода, а сразу выводятся
        else
            self::pullInStack(self::$scriptsСhunks, $string);
    }


    public static function printJsFiles()
    {
        $link = '';
        if (General::getConfigValue("collect_js_css", 1)) {
            foreach (self::$scripts as $script) {
                $link .= '<script type="text/javascript" src="';
                $link .= self::$libPuth . $script;
                $link .= '"></script>';
            }
        } else {
            foreach (self::$scripts as $script) {
                $link .= '<script type="text/javascript" src="';
                $link .= $script . '?' . OTBase::getVersion();
                $link .= '"></script>';
            }
        }
        foreach (self::$defaultScripts as $script) {
            $link .= '<script type="text/javascript" src="';
            $link .= $script . '?' . OTBase::getVersion();
            $link .= '"></script>';
        }

        return $link;
    }


    public static function printCssFiles()
    {
        $link = '';
        if (General::getConfigValue("collect_js_css", 1)) {
            foreach (self::$styles as $style) {
                $link .= '<link type="text/css" rel="stylesheet" href="';
                $link .= self::$libPuth . $style;
                $link .= '" />';
            }
        } else {
            foreach (self::$styles as $style) {
                $link .= '<link type="text/css" rel="stylesheet" href="';
                $link .= $style . '?' . OTBase::getVersion();
                $link .= '" />';
            }
        }

        return $link;
    }


    public static function printJs()
    {
        return implode('', self::$scriptsСhunks);
    }


    private static function includeConfigFile($path)
    {
        if (!array_key_exists($path, self::$assetsGroups)) {
            self::$assetsGroups[$path] = include CFG_APP_ROOT . $path . '/assets.php';
        }

        return self::$assetsGroups[$path];
    }


    public static function printJsFilesGroup($name, $path)
    {
        $link = '';

        if (General::getConfigValue("collect_js_css", 1)) {
            $link .= '<script type="text/javascript" src="';
            $link .= self::$libPuthGroups . $name . '&path=' . $path;
            $link .= '"></script>';
        } else {
            self::includeConfigFile($path); // загружает конфиг со списком файлов, если еще не загружен
            foreach (self::$assetsGroups[$path][$name] as $script) {
                $link .= '<script type="text/javascript" src="';
                $link .= substr($script,1) . '?' . OTBase::getVersion(); // обрезаем первый слеш из ссылки на файл
                $link .= '"></script>';
            }
        }

        return $link;
    }


    public static function printCssFilesGroup($name, $path)
    {
        $link = '';

        if (General::getConfigValue("collect_js_css", 1)) {
            $link .= '<link type="text/css" rel="stylesheet" href="';
            $link .= self::getCollectedCssFile($name, $path);
            $link .= '" />';
        }
        else {
            self::includeConfigFile($path); // загружает конфиг со списком файлов, если еще не загружен
            foreach (self::$assetsGroups[$path][$name] as $script) {
                $link .= '<link type="text/css" rel="stylesheet" href="';
                $link .= substr($script,1) . '?' . OTBase::getVersion(); // обрезаем первый слеш из ссылки на файл
                $link .= '" />';
            }
        }

        return $link;
    }

    /**
     * @param string $name
     * @param string $path
     * @return string
     */
    public static function getCollectedCssFile($name, $path)
    {
        return self::$libPuthGroups . $name . '&path=' . $path;
    }


    public static function jsBegin()
    {
        ob_start();
    }


    public static function jsEnd()
    {
        $strScript = ob_get_clean();
        return $strScript;
    }
}
