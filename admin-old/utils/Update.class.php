<?php

class Update {

    /**
     * Public
     */
    public function defaultAction() {
        require BASE_ADMIN_PATH.'lib/pclzip/pclzip.lib.php';
        $path = BASE_PATH.'updates/opentao.zip';
        $archive = new PclZip($path);

        if (!Login::auth()) {
            header('Location: index.php?expired');
            die;
        }
        $current_version = $this->getCurrentVersion();
        $versions = $this->getLatestInfo();
        //Получаем доступные бэкапы
        $recoverinfo = $this->GetRecoverInfo('../../');

        Session::set('updateVersion', (string)$versions->Version[0]->Number);

        ob_start();
        $c = curl_init(CFG_SUPPORT_URL.'/ru/test_site_speed/update_silent_check/'.
            $versions->Version[0]->Number);
        if(! ini_get('safe_mode') && ! ini_get('open_basedir') && ! defined('CFG_NO_CURLOPT_FOLLOWLOCATION')) {
            curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
        }
        curl_setopt($c, CURLOPT_REFERER, 'http://' . $_SERVER['HTTP_HOST'] . '/admin-old/');
        curl_exec($c);
        $allow_update = ob_get_contents();
        ob_end_clean();

        include(TPL_PATH . 'update.php');
    }

    public function extractAction() {
        @define('NO_DEBUG', 1);
        if (!Login::auth()) {
            print json_encode(array('success'=>false, 'error' => LangAdmin::get('session_has_ended')));
            return ;
        }
        require dirname(dirname(__FILE__)).'/lib/pclzip/pclzip.lib.php';
        $path = dirname(dirname(dirname(__FILE__))).'/updates/opentao.zip';
        $archive = new PclZip($path);
        chdir(dirname(dirname(dirname(__FILE__))));

        if (file_exists('install')) {
            General::rrmdir('install');
        }

        $archive->extract(PCLZIP_OPT_BY_PREG, "/^install.*/");

        if (file_exists('install')) {
            die('Ok');
        }

        if(!class_exists('ZipArchive', false))
            die(Lang::get('manual_install'));

        $zip = new ZipArchive;
        if ($zip->open($path) === TRUE) {
            $zip->extractTo('updates');
            $zip->close();
        } else {
            die(Lang::get('manual_install'));
        }

        print Lang::get('manual_install');
        die();
    }

    public function downloadAction() {
        @define('NO_DEBUG', 1);
        if (!Login::auth()) {
            print json_encode(array('success'=>false, 'error' => LangAdmin::get('session_has_ended')));
            return ;
        }
        $versions = $this->getLatestInfo();
        $res = $this->downloadFile(CFG_TOOLS_URL . '/update_rep/zips/opentao_'.$versions->Version[0]->Revision.'.zip', BASE_DIR.'../updates/opentao.zip');

        print $res;
    }


    public function RecoverAction($request) {
        //Распаквываем архивчиг

        if(@$request->getValue('file')) {
            chdir(dirname(dirname(dirname(__FILE__))));
            require_once 'admin-old/lib/pclzip/pclzip.lib.php';
            $path = 'backup/'.$request->getValue('file');
            $archive = new PclZip($path);
            $list = $archive->extract(PCLZIP_CB_PRE_EXTRACT, 'ExtractCallBack');

            if(!$list){
                print Lang::get('Extracting_is_fail_Service_information') . ':<br />' . "ERROR : " . $archive->errorInfo(true);
            } else {
                print 'Данные успешно восстановлены.';
            }

        } else {
            print 'Ошибка, не задан файл бэкапа.';
        }
        die();


    }

    /**
     * Private
     */
    private function getCurrentVersion() {
        $path = dirname(dirname(dirname(__FILE__))) . '/updates/version.xml';
        if (file_exists($path)) {
            $v = simplexml_load_file($path);
        } else {
            $v = 0;
        }
        return $v;
    }

    private function getLatestInfo()
    {
        $url = CFG_TOOLS_URL . '/update_rep/info/info.php?phpVersion=' . phpversion();
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        $contents = curl_exec($ch);
        if (curl_errno($ch)) {
            echo curl_error($ch);
            echo "\n<br />";
            $contents = '';
        } else {
            curl_close($ch);
        }

        if (! is_string($contents) || ! strlen($contents)) {
            echo "Failed to get available versions.";
            $contents = '';
        }
        return simplexml_load_string(trim($contents));
    }

    private function downloadFile($url, $path) {

        $newfname = $path;
        $file = @fopen($url, "rb");
        if ($file) {
            $newf = @fopen($newfname, "wb");

            if ($newf){
                while (!feof($file)) {
                    fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
                }
            }
            else{
                return json_encode(array('success'=>false, 'error' => LangAdmin::get('do_not_burn_the_backup_folder').' updates. '.LangAdmin::get('check_the_write_permissions_on_the_folder_and_set_the').' 777'));
            }
        }
        else{
            return json_encode(array('success'=>false, 'error' => LangAdmin::get('there_is_no_archive_of_updates')));
        }

        if ($file) {
            fclose($file);
        }

        if ($newf) {
            fclose($newf);
        }
        return json_encode(array('success'=>true));
    }

    private function GetRecoverInfo($path) {
        $returnarray = array();
        $dir = $path."backup/";   //задаём имя директории
        if(is_dir($dir)) {   //проверяем наличие директории
            $files = scandir($dir);    //сканируем (получаем массив файлов)
            array_shift($files); // удаляем из массива '.'
            array_shift($files); // удаляем из массива '..'
            //Выдираем по маске файлы архивов
            foreach( $files as $file ) {
                if (substr_count($file, '.zip'))
                   $tmp['name'] = $file;
                   $tmp['date'] = fileatime ($path."backup/".$file);
                   $returnarray[] = $tmp;
            }

        }
        return $returnarray;
    }


}

function ExtractCallBack($p_event, &$p_header)  {
        $info = pathinfo($p_header['filename']);
        @unlink($info['dirname'] .'/'. $info['basename']);
        return 1;
}

?>
