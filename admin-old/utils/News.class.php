<?php

OTBase::import('system.uploader.php.UploadHandler');

class News {

    public function __construct()
    {
        if (defined('NO_REDIRECT_TO_NEW_ADM')) {
            return;
        }

        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }

        $newLink = 'http://' . HOST_NAME . '/admin/?cmd=contents&do=news';

        include(TPL_DIR . 'redirect_to_new_admin.php');
        die;
    }
    /**
     * Public
     */
    public function defaultAction()
    {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }
        $cms = new CMS();
        $status = $cms->Check();
        if ($status)
        {
                        $news = $cms->GetAllNews();
            if ($news === -1) $news = $cms->GetAllNews();
        } else {
            include(TPL_DIR . 'cms.php');
            die;
        }

        include(TPL_DIR . 'news.php');
    }

    public function editAction()
    {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }
        $cms = new CMS();
        $status = $cms->Check();
        if ($status)
        {
            $id = $_GET['id'];
            settype($id, 'int');
            $news = $cms->GetNewsByID($id);

            $webui = $otapilib->GetWebUISettings($sid);
        } else {
            include(TPL_DIR . 'cms.php');
            die;
        }

        include(TPL_DIR . 'cms/editnews.php');
    }

    private function setActiveLang(){
        if(@$_GET['lang'])
            $_SESSION['menu_lang'] = @$_GET['lang'];
        if(!@$_SESSION['menu_lang']){
            $_SESSION['menu_lang'] = 'en';
        }
        return $_SESSION['menu_lang'];
    }

    public function addAction()
    {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }
        $cms = new CMS();
        $status = $cms->Check();
        if ($status)
        {
            $id = 'new';
            $news = array('id' => 'new');

            $webui = $otapilib->GetWebUISettings($sid);

        } else {
            include(TPL_DIR . 'cms.php');
            die;
        }

        include(TPL_DIR . 'cms/editnews.php');
    }

    public function delAction()
    {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }
        $cms = new CMS();
        $status = $cms->Check();
        if ($status)
        {
            $id = $_GET['id'];
            settype($id, 'int');
            $cms->DeleteNewsByID($id);
            header('Location: ?cmd=news');
        }
        header('Location: ?cmd=news');
    }

    public function editsaveAction()
    {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }
        $cms = new CMS();
        $status = $cms->Check();
        if ($status) {
            $id = $_POST['id'];
            if ($id === 'new') {
                if (empty($_POST['title'])) {
                    header('Location: ?cmd=news');
                    die;
                }
                $_POST['image'] = '';
                $id = $cms->CreateNews($_POST);
            }
            $_POST['image'] = $this->getNameUploadImage($id);
            if (! $_POST['image']) {
                $_POST['image'] = $_POST['existing_logo'];
            }
            $cms->UpdateNewsByID($id, $_POST);

            header('Location: ?cmd=news');
            die;
        }
        header('Location: ?cmd=news');
    }

    public function blocksaveAction()
    {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }
        $cms = new CMS();
        $status = $cms->Check();
        if ($status)
        {
            $id = $_POST['id'];
            settype($id, 'int');
            $cms->UpdateNewsText($id, $_POST['text']);

            if(@$_POST['id'])
                header('Location: ../?p=news&id='.$_POST['id']);
            else
                header('Location: ../?p=allnews');

            die;
        }
        if(@$_POST['pid'])
            header('Location: ../?p='.@$_POST['back'].'&pid='.$_POST['pid']);
        else
            header('Location: ../?p='.@$_POST['back']);
    }

    public function menuAction(){
        if (!Login::auth()){
            include(TPL_DIR.'login.php');
            return false;
        }

        $cms = new CMS();
        if (!$cms->Check()){
            include(TPL_DIR . 'db_connection_fail.php');
            die;
        }

        $cms->checkTable('site_langs');
        $cms->checkTable('site_translations');
        $cms->checkTable('site_translation_keys');
        $langs = $cms->getLanguages();
        $current_lang = $this->setActiveLang();
        $digestclass = new DigestRepository($cms);
        $all_docs = $digestclass->GetPagesByLang($current_lang);

        $cms->checkTable('site_blocks');
        $top_menu_json = $cms->getBlock('top_menu_'.$current_lang);

        $top_menu = array();
        if($top_menu_json){
            $top_menu = json_decode($top_menu_json);
        }

        include(TPL_DIR . 'menu/index.php');
    }

    private function uploadImage($id)
    {
        $uploader = new UploadHandler(array(
            'param_name' => 'uploaded_logo',
            'image_versions' => array(
                'large' => array(
                    'max_width' => 800,
                    'max_height' => 600,
                    'jpeg_quality' => 95,
                    'name' => 'large'
                ),
                'big' => array(
                    'max_width' => 300,
                    'max_height' => 200,
                    'jpeg_quality' => 95,
                    'name' => 'big'
                ),
                'thumb' => array(
                    'max_width' => 100,
                    'max_height' => 100,
                    'jpeg_quality' => 90,
                    'name' => 'thumb'
                )
            ),
        ), false, null, '/uploaded/news/' . $id . '/');
        return $uploader->post(false);
    }

    private function getNameUploadImage($id)
    {
        if (! empty($_FILES['uploaded_logo']['tmp_name'])) {
            $uploadResult = $this->uploadImage($id);
            if (isset($uploadResult['uploaded_logo'][0])) {
                if (isset($uploadResult['uploaded_logo'][0]->url)) {
                    //Выятгиваем расширение
                    $path_info = pathinfo($uploadResult['uploaded_logo'][0]->url);
                    $logoUrl = '/uploaded/news/' . $id . '/replace.' . $path_info['extension'];
                } else if (isset($uploadResult['uploaded_logo'][0]->error)) {
                    Session::setError($uploadResult['uploaded_logo'][0]->error, 'UploaderError');
                }
            } else {
                Session::setError('Unknown error occured while uploading image. Try again.', 'UploaderError');
            }
        } else {
            $logoUrl = '';
        }
        return $logoUrl;
    }
}

?>
