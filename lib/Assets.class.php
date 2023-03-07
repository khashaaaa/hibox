<?php

class Assets
{
    private static $scripts = array();
    private static $styles = array();
    private static $collectionIds = array(
        'admin_css_vendor_collection' => '/admin/cfg/admin_css_vendor_collection.php',
        'admin_css_ot_collection' => '/admin/cfg/admin_css_ot_collection.php',
        'admin_js_collection' => '/admin/cfg/admin_js_collection.php',
    );

    private static function pullInStack(&$stack, $source)
    {
        if (! in_array($source, $stack)) {
            $stack[] = $source;
        }
    }

    public static function addScript($scriptSource)
    {
        self::pullInStack(self::$scripts, $scriptSource);
    }

    public static function addStyle($styleSource)
    {
        self::pullInStack(self::$styles, $styleSource);
    }

    public static function getScripts()
    {
        return self::$scripts;
    }

    public static function getStyles()
    {
        return self::$styles;
    }

    public static function getCollectionPath($id)
    {
        if (isset(self::$collectionIds[$id])) {
            return self::$collectionIds[$id];
        }
        return null;
    }

    public static function includeCollectionJs($id, $options)
    {
        $configPath = CFG_APP_ROOT . self::getCollectionPath($id);
        if (! file_exists($configPath)) {
            return '';
        } elseif ($options['glue']) {
            // включено кеширование и склеивание скриптов
            $path = (isset($options['compressUrl'])) ? $options['compressUrl'] : '/js';
            $url = $path . '/compress.php?id=' . $id . '&lang=' . $options['lang'] . '&ver=' . $options['version'];
            if ($options['compress']) {
                $url .= '&compress=1';
            }
            return '<script src="' . $url . '"></script>';
        } else {
            $result = '';

            $sources = array();
            require_once $configPath; // получаем массив $sources

            foreach ($sources as $source) {
                $result .= '<script src="' . $source . '?' . $version .'"></script>' . "\n";
            }

            return $result;
        }
    }

    public static function includeCollectionCss($id, $options)
    {
        $configPath = CFG_APP_ROOT . self::getCollectionPath($id);
        if (! file_exists($configPath)) {
            return '';
        } elseif ($options['glue']) {
            // включено кеширование и склеивание скриптов
            $path = (isset($options['compressUrl'])) ? $options['compressUrl'] : '/css';
            $url = $path . '/compress.php?id=' . $id . '&lang=' . $options['lang'] . '&ver=' . $options['version'];
            if ($options['compress']) {
                $url .= '&compress=1';
            }
            return '<link rel="stylesheet" href="' . $url . '" />';
        } else {
            $result = '';

            $sources = array();
            require_once $configPath; // получаем массив $sources

            foreach ($sources as $source) {
                $result .= '<link rel="stylesheet" href="' . $source . '?' . $options['version'] .'" />' . "\n";
            }

            return $result;
        }
    }
}
