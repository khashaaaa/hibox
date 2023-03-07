<?php

class My_goods
{

    function defaultAction(){
        global $otapilib;

        if (!Login::auth()){
            include(TPL_DIR.'login.php');
            return false;
        }

        $cms = new CMS();
        if (!$cms->Check()){
            include(TPL_DIR . 'db_connection_fail.php');
            die;
        }

        $cms->checkTable('my_goods');
        $cms->checkTable('my_categories');
		$category_id = isset($_GET['cat']) ? (int) $_GET['cat']	: 0;

		$categories = $cms->GetCategoryById();

		$goods = $cms->GetGoodsByCatId($category_id);

        $current_lang = $this->setActiveLang();
        $translations = $cms->getTranslations('', $current_lang);
        include(TPL_DIR . 'goods/index.php');

    }

    private function setActiveLang(){
        if(@$_GET['lang'])
            $_SESSION['translate_lang'] = @$_GET['lang'];
        if(!@$_SESSION['translate_lang']){
            $_SESSION['translate_lang'] = 'en';
        }
        return $_SESSION['translate_lang'];
    }

    public function add_goodsAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = $_SESSION['sid'];
            $userid = @$_GET['id'];

            $result = $otapilib->GetWebUISettings($sid);
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }

            include(TPL_DIR . 'goods/edit.php');
        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    public function saveAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = $_SESSION['sid'];

            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }

            $cms = new CMS();
            $status = $cms->Check();

            if ($status)
            {
                $cms->AddOrUpdateMyGoods();
            }
            header('Location:index.php?cmd=my_goods&cat=' . (isset($_REQUEST['cat']) ? $_REQUEST['cat'] : 0));

        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    public function edit_goodAction()
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

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($status && $id)
        {
            $goods = $cms->GetGoodsById($id);
        } else {
            include(TPL_DIR . 'index.php');
            die;
        }

        include(TPL_DIR . 'goods/edit.php');
    }

    public function del_goodAction()
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

        $id = isset($_GET['id']) ? (int) $_GET['id'] : NULL;
		try {
			$result = $cms->DeleteRow('my_goods', $id);
		} catch (Exception $e) {
			echo $e->getMessage();
		}
        if ($result) echo 'Ok';
        die;
	}

}
