<?php

class Cmsadmin {
    /**
     * Public
     */

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

        $newLink = 'http://' . HOST_NAME . '/admin/?cmd=contents&do=default';

        if (isset($_GET['do']) && $_GET['do'] == 'menu') {
            $newLink = 'http://' . HOST_NAME . '/admin/?cmd=contents&do=navigation';
        }

        include(TPL_DIR . 'redirect_to_new_admin.php');
        die;
    }

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
        if ($status) {
//            $pages = $cms->GetPages();
//            if ($pages === -1) $pages = $cms->GetPages();

            $cRep = new ContentRepository($cms);
            $allPages = $cRep->GetPages();
            $pages = array();

            // выбираем родителей
            foreach ($allPages as $page) {
                $parent = $cms->get_parent_id_site_pages_parents_page_id($page['id']);
                if (!$parent) {
                    $pages[$page['id']] = $page;
                    $pages[$page['id']]['children'] = array();
                }
            }
            // добавляем потомков к родителям
            foreach ($allPages as $page) {
                $parent = $cms->get_parent_id_site_pages_parents_page_id($page['id']);
                if ($parent) {
                    $pages[$parent]['children'][] = $page;
                }
            }
        } else {
            include(TPL_DIR . 'cms.php'); die();
        }
        include(TPL_DIR . 'cms.php');
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
            $page = $cms->GetPageByID($id);

            $webui = $otapilib->GetWebUISettings($sid);
        } else {
            include(TPL_DIR . 'cms.php');
            die;
        }

        include(TPL_DIR . 'cms/editpage.php');
    }

    private function setActiveLang()
    {
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
            header('Location: index.php?expired'); die();
        }
        $cms = new CMS();
        $status = $cms->Check();
        if ($status)
        {
            $id = 'new';
            $page = array('id' => 'new');
        } else {
            include(TPL_DIR . 'cms.php');
            die;
        }

        include(TPL_DIR . 'cms/editpage.php');
    }

    public function addSubPageAction()
    {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired'); die();
        }
        $cms = new CMS();
        $status = $cms->Check();
        if ($status)
        {
            $id = 'new';
            $page = array('id' => 'new');
            $sid = @$_SESSION['sid'];
            $webui = $otapilib->GetWebUISettings($sid);

            include(TPL_DIR . 'cms/editpage.php');
        } else {
            include(TPL_DIR . 'cms.php');
            die;
        }
    }

    public function delAction()
    {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired'); die();
        }
        $cms = new CMS();
        $status = $cms->Check();
        if ($status)
        {
            $id = (int)$_GET['id'];
//            settype($id, 'int');
            $cms->DeletePageByID($id);
            header('Location: ?cmd=cmsadmin'); die();
        }
        header('Location: ?cmd=cmsadmin'); die();
    }

    public function editsaveAction()
    {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired'); die();
        }
        $cms = new CMS();
        $status = $cms->Check();
        if ($status) {
            $id = ($_POST['id'] === 'new') ? $_POST['id'] : (int)$_POST['id'];
            if ($id === 'new') {
                if (empty($_POST['alias'])) {
                    header('Location: ?cmd=cmsadmin'); die();
                }
                $new_pageId = $cms->CreatePage($_POST);
                if (isset($_POST['parent'])) {
                    $cms->add_site_pages_parents((int)$new_pageId, (int)$_POST['parent']);
                }
            } else {
                $cms->UpdatePageByID($id, $_POST);
                if (isset($_POST['parent'])) {
                    $cms->del_site_pages_parents_page_id($id);
                    $cms->add_site_pages_parents($id, (int)$_POST['parent']);
                }
            }
            header('Location: ?cmd=cmsadmin'); die();
        }
        header('Location: ?cmd=cmsadmin'); die();
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
            $cms->UpdateBlockByID($id, $_POST['text']);

            if(@$_POST['pid'])
                header('Location: ../?p='.@$_POST['back'].'&pid='.$_POST['pid']);
            else
                header('Location: ../?p='.@$_POST['back']);

            die;
        }
        if(@$_POST['pid'])
            header('Location: ../?p='.@$_POST['back'].'&pid='.$_POST['pid']);
        else
            header('Location: ../?p='.@$_POST['back']);
    }

    // НЕ ПЕРЕНЕСЕН функционал с freetaobuy
    public function menuAction()
    {
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
        $cms->checkTable('site_blocks');
//        $blocks = array('site_langs', 'site_translations', 'site_translation_keys', 'site_blocks');
//        array_walk($blocks, array($this,'checkTable'));

        $langs = $cms->getLanguages();
        $current_lang = $this->setActiveLang();

//        $all_docs = $cms->GetPagesByLang($current_lang);
        $digestclass = new DigestRepository($cms);
        $allPages = $digestclass->GetPagesByLang($current_lang);
        $all_docs = array();
        foreach($allPages as $page){
//            $parent = @mysql_result(mysql_query('SELECT `parent_id` FROM `site_pages_parents` WHERE `page_id`='.$page['id']), 0);
            $parent = $cms->get_parent_id_site_pages_parents_page_id($page['id']);
            $page['children'] = array();
            if(!$parent)
                $all_docs[] = $page;
        }
        $all_docs[] = array('id' => 'calculator', 'title' => Lang::get('calculator'), 'alias' => 'calculator');

        $all_docs[] = array('id' => 'reviews', 'title' => Lang::get('reviews'), 'alias' => 'reviews');

        if(CMS::IsFeatureEnabled('Digest'))
            $all_docs[] = array('id' => 'digest', 'title' => LangAdmin::get('digest'), 'alias' => 'digest');
        if (CMS::IsFeatureEnabled('FleaMarket'))
            $all_docs[] = array('id' => 'pristroy', 'title' => LangAdmin::get('pristroy'), 'alias' => 'pristroy');
        if (CMS::IsFeatureEnabled('ShopComments'))
            $all_docs[] = array('id' => 'shopreviews', 'title' => LangAdmin::get('shopreviews'), 'alias' => 'shopreviews');

//        $top_menu_json = $cms->getBlock('top_menu_'.$current_lang);
//        $top_menu = array();
//        if($top_menu_json){
//            $top_menu = json_decode($top_menu_json);
//        }

        $top_menu_json = $cms->getBlock('top_menu_'.$current_lang);
        $top_menu = $top_menu_json ? json_decode($top_menu_json) : array();

        $left_menu_json = $cms->getBlock('left_menu_'.$current_lang);
        $left_menu = $left_menu_json ? json_decode($left_menu_json) : array();

        include(TPL_DIR . 'menu/index.php');
    }

    public function menusaveAction()
    {
        if(@$_POST['TopMenu']){
            if (get_magic_quotes_gpc())
                $_POST['TopMenu'] = stripslashes($_POST['TopMenu']);

            $current_lang = $this->setActiveLang();
            $topMenu = json_encode(CMS::removeNotAvailableMenuItems(json_decode($_POST['TopMenu'])));
            $type = 'top_menu_'.$current_lang;
            $q = mysql_query('SELECT COUNT(*) FROM `site_blocks` WHERE `type`="'.$type.'"');
            if(mysql_result($q, 0)){
                mysql_query('UPDATE `site_blocks` SET `properties`="'.  mysql_real_escape_string($topMenu).'" WHERE `type`="'.$type.'"');
            }
            else{
                mysql_query('INSERT INTO `site_blocks` SET `properties`="'.  mysql_real_escape_string($topMenu).'", `type`="'.$type.'"');
            }
        }

        if(@$_POST['LeftMenu']){
            if (get_magic_quotes_gpc())
                $_POST['LeftMenu'] = stripslashes($_POST['LeftMenu']);

            $leftMenu = json_encode(CMS::removeNotAvailableMenuItems(json_decode($_POST['LeftMenu'])));
            $current_lang = $this->setActiveLang();
            $type = 'left_menu_'.$current_lang;
            $q = mysql_query('SELECT COUNT(*) FROM `site_blocks` WHERE `type`="'.$type.'"');
            if(mysql_result($q, 0)){
                mysql_query('UPDATE `site_blocks` SET `properties`="'.  mysql_real_escape_string($leftMenu).'" WHERE `type`="'.$type.'"');
            }
            else{
                mysql_query('INSERT INTO `site_blocks` SET `properties`="'.  mysql_real_escape_string($leftMenu).'", `type`="'.$type.'"');
            }
        }

        header('Location: index.php?cmd=cmsadmin&do=menu');
    }

}

?>
