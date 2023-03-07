<?php

OTBase::import('system.uploader.php.UploadHandler');

class Digest {

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

        $newLink = 'http://' . HOST_NAME . '/admin/?cmd=blog&do=default';

        include(TPL_DIR . 'redirect_to_new_admin.php');
        die;
    }

    /**
     * Public
     */
    public function defaultAction()
    {
        global $otapilib;
        $cms = new CMS();
        $status = $cms->Check();
        $digestclass = new DigestRepository($cms);
        $sid = Session::get('sid');
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }

        if (isset($_GET['page'])) {
                $page = $_GET['page']-1;
        } else {
                $page = 0;
        }
        $page_count = 20;

        try {
            $digest = $digestclass->GetAllPosts($page*$page_count,$page_count);
            $CountPosts = $digestclass->GetCountPosts();
        } catch (DBException $e) {
            Session::setError($e->getMessage(), 'DBError');
        }

        include(TPL_DIR . 'digest.php');
    }

    public function editAction()
    {
        global $otapilib;
        $digestclass = new DigestRepository(new CMS());
        $sid = Session::get('sid');
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }

        $id = $_GET['id'];
        settype($id, 'int');
        try {
            $post = $digestclass->GetPostByID($id);
            $cats = $digestclass->getAllDigestCategories($digestclass->getLangCode($post['lang_id']));
        } catch (DBException $e) {
            Session::setError($e->getMessage(), 'DBError');
        }
        include(TPL_DIR . 'cms/editdigest.php');
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
        $digestclass = new DigestRepository(new CMS());
        $sid = Session::get('sid');
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }
        try {
            $post = array('id' => 'new');
            $cats = $digestclass->getAllDigestCategories('ru');
        } catch (DBException $e) {
            Session::setError($e->getMessage(), 'DBError');
        }
        include(TPL_DIR . 'cms/editdigest.php');
    }

    public function delAction()
    {
        global $otapilib;
        $digestclass = new DigestRepository(new CMS());
        $sid = Session::get('sid');
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }
        $id = $_GET['id'];
        settype($id, 'int');
        try {
            $digestclass->DeletePostByID($id);
        } catch (DBException $e) {
            Session::setError($e->getMessage(), 'DBError');
        }
        header('Location: ?cmd=digest');
    }

    public function editsaveAction()
    {
        global $otapilib;
        $digestclass = new DigestRepository(new CMS());
        $sid = Session::get('sid');
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '') {
            header('Location: index.php?expired');
            die;
        }
        $id = $_POST['id'];
        if ($id === 'new')  {
            if (empty($_POST['title'])) {
               header('Location: ?cmd=digest');
               die;
            }
            try {
                $_POST['image'] = '';
                $id = $digestclass->CreatePost($_POST);
            } catch (DBException $e) {
                Session::setError($e->getMessage(), 'DBError');
            }
        }
        $_POST['image'] = $this->getNameUploadImage($id);
        if (! $_POST['image']) {
            $_POST['image'] = $_POST['existing_logo'];
        }
        try {
            $digestclass->UpdatePostByID($id, $_POST);
        } catch (DBException $e) {
            Session::setError($e->getMessage(), 'DBError');
        }

        header('Location: ?cmd=digest');
        die;

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
        try {
            $all_docs = $digestclass->GetPagesByLang($current_lang);
        } catch (DBException $e) {
            Session::setError($e->getMessage(), 'DBError');
        }

        $top_menu_json = $cms->getBlock('top_menu_'.$current_lang);

        $top_menu = array();
        if($top_menu_json){
            $top_menu = json_decode($top_menu_json);
        }

        include(TPL_DIR . 'menu/index.php');
    }

        public function addCategoryAction($request) {
            $digestclass = new DigestRepository(new CMS());
            try {
                $digestclass->CreateDigestCategory($request->get('title'), $request->get('desc'), $request->get('lang'));
                $cats = $digestclass->getAllDigestCategories($request->get('postLang'));
            } catch (DBException $e) {
                $this->throwAjaxError($e);
            }
            echo json_encode($cats);
            die();
        }

        public function editCategoryAction($request) {
            $digestclass = new DigestRepository(new CMS());
            try {
                $digestclass->UpdateDigestCategory($request->get('title'), $request->get('desc'), $request->get('lang'), $request->get('id'));
                $cats = $digestclass->getAllDigestCategories($request->get('postLang'));
            } catch (DBException $e) {
                $this->throwAjaxError($e);
            }
            echo json_encode($cats);
            die();
        }

        public function deleteCategoryAction($request) {
            $digestclass = new DigestRepository(new CMS());
            try {
                $digestclass->DeleteDigestCategory($request->get('id'));
                $cats = $digestclass->getAllDigestCategories($request->get('postLang'));
            } catch (DBException $e) {
                $this->throwAjaxError($e);
            }
            echo json_encode($cats);
            die();
        }

        public function getCategoriesByLangAction($request) {
            $digestclass = new DigestRepository(new CMS());
            try {
                $cats = $digestclass->getAllDigestCategories($request->get('postLang'));
            } catch (DBException $e) {
                $this->throwAjaxError($e);
            }
            echo json_encode($cats);
            die();
        }

        public function getitemsAction() {
            global $otapilib;
            $results = "";

            if (isset($_GET['ids'])) {
                $ids = $_GET['ids'];
                $results = @$otapilib->GetItemInfo($ids);
            }

            if ($results) {

                    $post_page = '<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%; " width="624"><tr>
                                        <td><p align="center"><a href="index.php?p=item&id='.$results['id'].'">'.$results['title'].'</a></p></td>
                                 </tr><tr>
                                        <td><p align="center"><a href="index.php?p=item&id='.$results['id'].'"><img src="' .$results['mainpictureurl'].'" width="310px" height="310px"></a></p></td>
                                 </tr><tr>
                                        <td><p align="center"><strong>'.$results['convertedprice'].'</strong></p></td>
                                </tr></table>';
                    echo $post_page;
                }
                die();
        }

        function drawTD($data) {
            $post_page = '';
            $post_page .= '<td style="height:24px;">';
            $post_page .= '<p align="center"><a href="index.php?p=' . $data['id'] . '">' . $data['title'] . '</a></p>';
            $post_page .= '<p align="center"><a href="index.php?p=' . $data['id'] . '">';
            $post_page .= '<img src="' . $data['mainpictureurl'] . '" width="310px" height="310px">';
            $post_page .= '</a></p>';
            $post_page .= '<p align="center" style="margin-left:-1.5pt;"><strong>' . $data['Price']['ConvertedPrice'] . ' ' . $data['Price']['currencyname'] . '</strong></p>';
            $post_page .= '</td>';
            return $post_page;
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
        ), false, null, '/uploaded/blogs/' . $id . '/');
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
                    $logoUrl = '/uploaded/blogs/' . $id . '/replace.' . $path_info['extension'];
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
