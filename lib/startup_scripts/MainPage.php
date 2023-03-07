<?php

OTBase::import('system.lib.FileAndMysqlMemoryCache');
OTBase::import('system.lib.CMS');
OTBase::import('system.lib.General');
OTBase::import('system.lib.Logger');
OTBase::import('system.lib.Session');
OTBase::import('system.otapilib2.OTAPILib2');
OTBase::import('system.otapilib2.types.OtapiAnswer');

class MainPage
{
    private static $shownErrorCodes = array('InstanceKeyBan', 'CallLimit');
    private static $cacheDir = 'mainpage';
    private static $cacheFile = 'backup.dat';

    public static function backup($pageHtml = null)
    {
        if (isset($_GET['__backup_main_page_process'])) {
            return;
        }

        $dir = self::getCacheDir();

        try {
            if (! $pageHtml) {
                $url = UrlGenerator::getProtocol() . '://' . $_SERVER['HTTP_HOST'] . '/?__backup_main_page_process';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
                $pageHtml = curl_exec($ch);
                if (curl_errno($ch)) {
                    $pageHtml = null;
                }
                curl_close($ch);
            }

            $isPageLoaded =
                (strpos($pageHtml, 'data-info="side-menu-loaded"') !== false) &&
                (strpos($pageHtml, 'data-info="index-sets-loaded"') !== false) &&
                (strpos($pageHtml, 'data-info="index-footer-loaded"') !== false);

            if ($isPageLoaded) {
                file_put_contents($dir . '/' . self::$cacheFile, $pageHtml);
            }

        } catch (Exception $e) {}
    }

    public static function getBackup()
    {
        $file = self::getCacheDir() . '/' . self::$cacheFile;
        if (file_exists($file)) {
            return file_get_contents($file);
        }
    }

    private static function getCacheDir()
    {
        $dir = CFG_APP_ROOT . '/cache/' . self::$cacheDir;
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }
}
