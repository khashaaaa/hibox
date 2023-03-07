<?php

class RenderAdminHead
{
    const urlRequest = '/lib/RenderAdminHead/request.php';
    const urlSupportApi = 'https://support.otcommerce.com/api';
    const urlSupportRequest = '/opentao/box_important_message/get';

    public static function onRenderAdminHead()
    {
        try {
            $cms = new CMS();
            $cms->Check();
            $fileAndMysqlMemoryCache = new FileAndMysqlMemoryCache($cms);

            // if isset cache
            $idCache = 'RenderAdminHead:getImportantMessage' . Session::getActiveAdminLang();
            if ($fileAndMysqlMemoryCache->Exists($idCache)) {
                // get cache
                $cache = $fileAndMysqlMemoryCache->GetCacheEl($idCache);
                $response = json_decode($cache);
                return '<div class="important_message" id="important_message">' . (string)$response->message . '</div>';
            }

            ob_start();
            $urlRequest = UrlGenerator::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . self::urlRequest;
            require_once dirname(__FILE__) . '/tpl/index.php';
            $tpl = ob_get_contents();
            ob_end_clean();
            return $tpl;
        } catch (Exception $e) {
            //
        }

        return '';
    }

    public static function getImportantMessage($params)
    {
        try {
            $cms = new CMS();
            $cms->Check();
            $fileAndMysqlMemoryCache = new FileAndMysqlMemoryCache($cms);

            $curl = new Curl(self::urlSupportApi . self::urlSupportRequest, 60, true);
            $curl->setPost(http_build_query($params), false);
            $curl->connect();

            if ($curl->getHttpStatus() == 200) {
                $response = $curl->getWebPage();
                $idCache = 'RenderAdminHead:getImportantMessage' . Session::getActiveAdminLang();
                $fileAndMysqlMemoryCache->AddCacheEl($idCache, 300, $response);
                return $response;
            }
        } catch (Exception $e) {
            //
        }

        return '';
    }
}
