<?php

require_once 'vendor/JSMin.class.php';
require_once 'vendor/CssMin.class.php';
defined('CFG_APP_ROOT') or define('CFG_APP_ROOT', dirname(dirname(__FILE__)));

class CodeCompress
{
    public static function getJSCode($scripts, $name, $ver, $compress = false, $useCache= true)
    {
        $cacheDir = CFG_APP_ROOT . '/cache';
        $cacheFileName = $cacheDir . '/' . $name . '-' . $ver . '.js';

        if ($useCache && file_exists($cacheFileName)) {
            return file_get_contents($cacheFileName);
        }

        $jsCode = self::glueResourceCode($scripts);
        if ($compress) {
            $jsCode = JSMin::minify($jsCode);
        }

        if ($useCache) {
            $createDirResult = !file_exists($cacheDir) ? mkdir($cacheDir, 0777, true) : true;
            file_put_contents($cacheFileName, $jsCode);
        }

        return $jsCode;
    }

    public static function getCSSCode($cssList, $name, $ver, $compress = false, $useCache= true)
    {
        $cacheDir = CFG_APP_ROOT . '/cache';
        $cacheFileName = $cacheDir . '/' . $name . '-' . $ver . '.css';

        if ($useCache && file_exists($cacheFileName)) {
            return file_get_contents($cacheFileName);
        }

        $cssList = self::glueResourceCode($cssList);
        if ($compress) {
            $cssList = CssMin::minify($cssList);
        }

        if ($useCache) {
            $createDirResult = !file_exists($cacheDir) ? mkdir($cacheDir, 0777, true) : true;
            file_put_contents($cacheFileName, $cssList);
        }

        return $cssList;
    }

    private static function glueResourceCode($resource)
    {
        $code = '';
        foreach ($resource as $source) {
            $sourcePath = CFG_APP_ROOT . $source;
            $code .= file_get_contents($sourcePath);
            $code .= "\n";
        }
        return $code;        
    }
}
