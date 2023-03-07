<?php
/*
 * <?=LangAdmin::get('the_class_forms_a_collection_of_goods')?>
 * 1 -  Best
 * 2 - Popular
 * 3 - Recommend
 */

include dirname(__FILE__).'/GeneralUtil.class.php';

class Randomset extends GeneralUtil {
    
    function defaultAction()
    {
        if (Login::auth())
        {
            global $otapilib;
            $sid = $_SESSION['sid'];
            
            $set1 = $otapilib->GetItemRatingList('Recommend', 20, 'set1');
            
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }
            
            $set2 = $otapilib->GetItemRatingList('Recommend', 20, 'set2');
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }
            
            $set3 = $otapilib->GetItemRatingList('Recommend', 20, 'set3');
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }
            
            $set4 = $otapilib->GetItemRatingList('Recommend', 20, 'set4');
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }
            
            $hidden_s1 = isset($_COOKIE['HiddenS1']) ? $_COOKIE['HiddenS1'] : 1;
            $hidden_s2 = isset($_COOKIE['HiddenS2']) ? $_COOKIE['HiddenS2'] : 1;
            $hidden_s3 = isset($_COOKIE['HiddenS3']) ? $_COOKIE['HiddenS3'] : 1;
            $hidden_s4 = isset($_COOKIE['HiddenS4']) ? $_COOKIE['HiddenS4'] : 1;

            include(TPL_DIR.'randomset/sets.php');

            if(isset($_SESSION['error'])) unset($_SESSION['error']);
        } else {
            include(TPL_DIR.'login.php');
        }
    }
    
    function savestatAction() {
        Cookie::set("HiddenS1", @$_GET['stats1'], time()+3600*24*30);
        Cookie::set("HiddenS2", @$_GET['stats2'], time()+3600*24*30);
        Cookie::set("HiddenS3", @$_GET['stats3'], time()+3600*24*30);
        Cookie::set("HiddenS4", @$_GET['stats4'], time()+3600*24*30);
    }
    
    function getItemInfoAction() {
        @define('NO_DEBUG', 1);
        global $otapilib;
        $item = $otapilib->GetItemFullInfo($_GET['id']);
        print json_encode( $item );
    }
    
    function getItemDescrAction() {
        @define('NO_DEBUG', 1);
        global $otapilib;
        $item = $otapilib->GetItemDescription($_GET['id']);
        print json_encode( $item );
    }
    
    function saveTitleAction() {
        @define('NO_DEBUG', 1);
        global $otapilib;
        $sid = $_SESSION['sid'];
        $key = "taobao:Item:Title";
        $res = $otapilib->EditTranslateByKey($sid, 'ru', $_POST['descr'], $key, $_POST['id']);
        if (!$res) {
            print $otapilib->error_message;
        } else {
            print LangAdmin::get('transfer_saved_successfully');
        }
    }
    
    function saveDescrAction() {
        @define('NO_DEBUG', 1);
        global $otapilib;
        $sid = $_SESSION['sid'];
        $key = "taobao:Item:Description";
        $res = $otapilib->EditTranslateByKey($sid, 'ru', $_POST['descr'], $key, $_POST['id']);
        if (!$res) {
            print $otapilib->error_message;
        } else {
            print LangAdmin::get('transfer_saved_successfully');
        }
    }


    function addAction() {
        if (Login::auth()) {
            global $otapilib;
            
            $sid = $_SESSION['sid'];
            $_SESSION['clear_cache'] = 1;

            $itemid = @$_POST['itemid'];
            $type = 'Recommend';
            $catid = @$_POST['catid'];

            if (preg_match( '/(http)/i', $itemid )) {
                $url = parse_url($itemid);
                $params = $this->_parse_query($url['query']);
                $itemid = $params['id'];
            }
            $result = $otapilib->AddElementsSetToRatingList($sid, $type,'Item', $catid, $itemid);
            $active_tab = 1;
            
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }
            header('Location: index.php?cmd=randomset&sid=&active_tab='.$active_tab);
        } else {
            include(TPL_DIR.'login.php');
        } 
    }
    
    function delAction() {
        if (Login::auth()) {
            global $otapilib;

            $_SESSION['clear_cache'] = 1;
            $sid = $_SESSION['sid'];
            
            $itemList = @$_GET['id'];
            $itemRatingTypeId = 'Recommend';
            $contentType = 'Item';
            $categoryId = @$_GET['catid'];;

            $result = $otapilib->RemoveElementsSetRatingList($sid, $itemRatingTypeId, $contentType, $categoryId, $itemList);
            $active_tab = 1;
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }

            header('Location: index.php?cmd=randomset&sid=&active_tab='.$active_tab);

        } else {
            include(TPL_DIR.'login.php');
        } 
    }
    
    function delallAction() {
        if (Login::auth()) {
            global $otapilib;

            $_SESSION['clear_cache'] = 1;
            $sid = $_SESSION['sid'];
            $type = 'Recommend';
            $catid = @$_POST['catid'];
            $contentType = 'Item';
            
            $result = $otapilib->RemoveAllElementsRatingList($sid, $type, $contentType, $catid);

            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }
            header('Location: index.php?cmd=randomset&sid=');
            
        } else {
            include(TPL_DIR.'login.php');
        } 
    }


    function saveorderAction() {
        if (Login::auth()) {
            global $otapilib;

            $_SESSION['clear_cache'] = 1;
            $sid  = $_SESSION['sid'];
            $type = 'Recommend';
            $catid = @$_POST['catid'];
            $ids  = @$_POST['ids'];
            $contentType = 'Item';

            $result = $otapilib->RemoveAllElementsRatingList($sid, $type, $contentType, $catid);

            if($result) {
                $result = $otapilib->AddElementsSetToRatingList($sid, $type, $contentType, $catid, $ids);
            }

            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }

            header('Location: ' . $_POST['return']);
            
        } else {
            include(TPL_DIR.'login.php');
        } 
    }


    private function _parse_query($var) {
        $var  = html_entity_decode($var);
        $var  = explode('&', $var);
        $arr  = array();

        foreach($var as $val) {
            $x          = explode('=', $val);
            $arr[$x[0]] = $x[1];
        }
        unset($val, $x, $var);
        return $arr;
    }
}