<?php

class Reviews {
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
        
        $perPage = (! empty($_GET['ps'])) ? $_GET['ps'] : 20;
        $pageNum = (! empty($_GET['p'])) ? $_GET['p'] : 1;
        $pageurl = 'index.php?cmd=reviews&ps=' . $perPage;
		$count=0;		
        $cms = new CMS();
        $status = $cms->Check();
        if ($status) {
            $repo = new ItemInfoRepository($cms);
			$comments = $repo->getNotAcceptedComments($perPage * ($pageNum - 1), $perPage);
			$count = $repo->getNumberOfComments();
            $pagination = Pagination::getPages($count, $perPage, $pageNum);
		} else {
            include(TPL_DIR . 'cms.php');
            die;
        }
        include(TPL_DIR . 'reviews.php');
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
            $id = intval($_GET['id']);
            $repo = new ItemInfoRepository($cms);
            $repo->deleteComment($id);
            header('Location: ?cmd=reviews');
        }
        header('Location: ?cmd=reviews');
    }
	public function acceptAction()
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
			$id = intval($_GET['id']);
            $repo = new ItemInfoRepository($cms);
			$repo->acceptComment($id);
			header('Location: ?cmd=reviews');
		}
		header('Location: ?cmd=reviews');
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
}

?>
