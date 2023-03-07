<?php

class Shopreview {
    /**
     * Public
     */
    public function defaultAction()
    {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '') {
            header('Location: index.php?expired');
            die;
        }
		$from = 0;
		$perpage = 10;
		$count = 0;

		if (isset($_GET['from'])) {
			$from = intval($_GET['from']);
		}
		$reviews = array();
        $cms = new CMS();		
		$status = $cms->Check();
		if ($status) {
        	$cms->checkTable('shop_reviews');
        	$res = mysql_query("SELECT * FROM `shop_reviews` ORDER BY `created` DESC LIMIT ".$from." , ".$perpage."");
			while ($row = mysql_fetch_assoc($res)) {   
				$reviews[] = $row;            
        	}
			$res = mysql_query("SELECT COUNT(*) FROM `shop_reviews`");
			$tmp =  mysql_fetch_array($res);
			$count = $tmp[0];
		} else {
            include(TPL_DIR . 'cms.php');
            die;
        }
		$pageurl = '/admin-old/index.php?cmd=shopreview';
        include(TPL_DIR . 'shopreview.php');
    }
    
    public function delAction()
    {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '') {
            header('Location: index.php?expired');
            die;
        }
        $cms = new CMS();
        $status = $cms->Check();
        if ($status) {
            $id = intval($_GET['id']);
            $res = mysql_query("DELETE FROM `shop_reviews` WHERE `review_id` = ".$id."");
			
            header('Location: ?cmd=shopreview');
        }
        header('Location: ?cmd=shopreview');
    }
	public function acceptAction()
	{
		global $otapilib;
		$sid = @$_SESSION['sid'];
		$webui = $otapilib->GetWebUISettings($sid);
		if ($otapilib->error_message == 'SessionExpired' || $sid == '') {
			header('Location: index.php?expired');
			die;
		}
		$cms = new CMS();
		$status = $cms->Check();
		if ($status) {
			$id = intval($_GET['id']);
			$res = mysql_query("UPDATE  `shop_reviews` SET  `accepted` =  '1' WHERE  `review_id` =".$id."");			
			header('Location: ?cmd=shopreview');
		}
		header('Location: ?cmd=shopreview');
	}
	
	public function answerAction()
	{
		if ($_POST) {
			global $otapilib;
			$sid = @$_SESSION['sid'];
			$webui = $otapilib->GetWebUISettings($sid);
			if ($otapilib->error_message == 'SessionExpired' || $sid == '') {
				header('Location: index.php?expired');
				die;
			}
			$cms = new CMS();
			$status = $cms->Check();
			if ($status) {	
				$id = intval($_POST['id']);
				$txt = $cms->escape($_POST['txt']);
				$res = mysql_query("UPDATE  `shop_reviews` SET  `answer_date` = CURRENT_TIMESTAMP,  `answer` =  '".$txt."' WHERE  `review_id` =".$id."");			
				header('Location: ?cmd=shopreview');
			}
			header('Location: ?cmd=shopreview');
		}
	}

	public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES);
    }
    
}

?>
