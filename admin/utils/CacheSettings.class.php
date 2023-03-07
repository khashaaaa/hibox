<?php

class CacheSettings extends GeneralUtil
{
    protected $_template = 'cache_settings';
    protected $_template_path = 'site_config/system/';

    public function defaultAction()
    {
        print $this->fetchTemplate();
    }

    public function cacheCleanAction()
    {
        Session::close();

        try {
            $this->otapilib->ResetInstanceCaches();
            Cacher::rRmDir(CFG_APP_ROOT . '/cache/', false);
            General::rRmDir(CFG_APP_ROOT . '/lock/', false);
        } catch (ServiceException $e) {
            $this->respondAjaxError(LangAdmin::get('reset_cache_fail') . ' ' . $e->getMessage());
        } catch (Exception $e) {
            // если очистили не весь кеш, а только часть вовзращает код ошибки 101
            if ($e->getCode() == 101) {
                $this->sendAjaxResponse(array(
                    'message' => LangAdmin::get('Cache_not_deleted_completly'),
                    'needRestart' => true
                ));
            } else {
                $this->respondAjaxError(LangAdmin::get('reset_cache_fail') . ' ' . $e->getMessage());
            }
        }

        $this->sendAjaxResponse(array(
            'message' => LangAdmin::get('reset_cache_success'),
            'needRestart' => false
        ));
    }

    /**
     * Возвращает размер папки "cache"
     * в мегабайтах
     */
    public function cacheSizeAction()
    {
        Session::close();

        $fileSize = $this->getFilesSize(CFG_APP_ROOT . '/cache/');

        if (!empty($fileSize)) {
            $fileSize = round(($fileSize / 1024) / 1024, 2) . 'Mb';
        } else {
            $fileSize = '';
        }

        $this->sendAjaxResponse(array(
            'message' => $fileSize
        ));
    }

    /**
     * Рекурсивно считает размер
     * директории/файла $path в байтах
     * @param string $path Директория/Файл
     * @return int Размер в байтах
     */
    private function getFilesSize($path)
    {
        $fileSize = 0;
        $dir = scandir($path);

        foreach ($dir as $file) {
            if (($file!='.') && ($file!='..')) {
                if (is_dir($path . '/' . $file)) {
                    $fileSize += $this->getFilesSize($path.'/'.$file);
                }
                else {
                    $fileSize += filesize($path . '/' . $file);
                }
            }
        }

        return $fileSize;
    }
}
