<?php
/*
 * <?=LangAdmin::get('the_class_forms_a_collection_of_goods')?>
 * 1 -  Best
 * 2 - Popular
 * 3 - Recommend
 */

class Brands extends GeneralUtil {
    
    private function setHidden(){
        if (isset($_COOKIE['HiddenRecom']))
        {
            $hidden_recom = @$_COOKIE['HiddenRecom'];
        } else {
            $hidden_recom = 1;
        }
        $hidden_popular = 1;
        return array($hidden_recom, $hidden_popular);
    }
    
    function defaultAction()
    {
        if (Login::auth())
        {
            global $otapilib;
            $sid = $_SESSION['sid'];
            $brandlist = $otapilib->GetBrandInfoFullList($sid);  			
            $brands = $brandlist;
			
            if ($otapilib->error_message == 'SessionExpired')
            {
                header('Location: index.php?expired');
                die;
            }
            list($hidden_recom, $hidden_popular) = $this->setHidden();
            if (isset($_GET['error'])) {
                $error = $_GET['error']; 
            }
            include(TPL_DIR.'brands.php');
        } else {
            include(TPL_DIR.'login.php');
        } 
    }
	///<!-- -->
	function GetBrandShortAction()
    {
        if(!defined('NO_DEBUG')) define('NO_DEBUG', true);
        global $otapilib;
        $sid = $_SESSION['sid'];
        $nme= $_POST['nme'];
        $brandsearclist = $otapilib->SearchOriginalBrandsFrame($sid,$nme);
        $brandsearch="<ul id=\"scr\">";
        foreach ($brandsearclist['content'] as $item) {
            $brandsearch.="<li><a onclick=\"SetBrand('{$item['name']}' , '{$item['id']}' , '{$item['description']}')\">".$item['name']."</a></li>";
        }
        $brandsearch.="</ul>";
        echo $brandsearch;

        if ($otapilib->error_message == 'SessionExpired')
        {
            header('Location: index.php?expired');
            die;
        }
    }
	
    ///<!-- -->
    function savestatAction()
    {
        Cookie::set('HiddenRecom', @$_GET['statr'], time()+3600*24*30);
        Cookie::set('HiddenPopular', @$_GET['statp'], time()+3600*24*30);
    }
    
    private function xmlParams(){
        $xmlParams = new SimpleXMLElement('<EditableBrandInfo></EditableBrandInfo>');
        $xmlParams->addChild('Name', htmlspecialchars(@$_POST['Name']));
        $xmlParams->addChild('PictureUrl', htmlspecialchars(@$_POST['PictureUrl']));
        $xmlParams->addChild('Description', htmlspecialchars(@$_POST['Description']));
        if(!@$_POST['ExternalId']){
            @$_POST['ExternalId'] = '1';
        }
        $xmlParams->addChild('ExternalId', htmlspecialchars(@$_POST['ExternalId']));
        if(@$_POST['IsNameSearch']){
            $xmlParams->addChild('IsNameSearch', 'true');
        }
        else{
            $xmlParams->addChild('IsNameSearch', 'false');
        }
        return str_replace('<?xml version="1.0" encoding="utf-8"?>','',$xmlParams->asXML());
    }
    
    function addAction(){
        if (Login::auth())
        {
            global $otapilib;
            
            $sid = $_SESSION['sid'];
            $xml = $this->xmlParams();
            
            $result = $otapilib->AddBrandInfo($sid, $xml);
            
            if (!$result)
            {
                $message = '&error='.$otapilib->error_message;
                header('Location: index.php?cmd=brands' . $message);
                die;
            }
			//Твйтл и дескприптион - НЕ ЗНАЕМ ЕГО ID  - НЕ СОХРАНИТЬ
			//$cms = new SafedCMS();   
			//$to_add = @$_POST;
			//$to_add['cid'] = $to_add[''];
            //$cms->callCMSMethod('setCategorySEO', array('data' => $to_add));
			//=======================
            header('Location: index.php?cmd=brands');
        } else {
            include(TPL_DIR.'login.php');
        } 
    }
    
    function saveAction(){
        if (Login::auth())
        {
            global $otapilib;
            
            $sid = $_SESSION['sid'];
            $xml = $this->xmlParams();
            
            $result = $otapilib->EditBrandInfo($sid, @$_POST['Id'], $xml);
            
            if (!$result)
            {
                $message = '&error='.$otapilib->error_message;
                header('Location: index.php?cmd=brands' . $message);
                die;
            }
			//Твйтл и дескприптион - сохраняем
			$to_edit = @$_POST;
			$to_edit['cid'] = $to_edit['Id'];
			try {
				$SeoCatsRepository = new SeoCategoryRepository(new CMS());			
            	$SeoCatsRepository->setCategorySEO($to_edit);
			} catch (DBException $e) {
                Session::setError($e->getMessage(), 'DBError');                
			}
			//=======================
            header('Location: index.php?cmd=brands');
        } else {
            include(TPL_DIR.'login.php');
        } 
    }
    
    function editAction(){
        if (Login::auth())
        {
            global $otapilib;
            
            $sid = $_SESSION['sid'];
            $brandData = $otapilib->GetBrandInfo(@$_GET['id']);
            
            if (!$brandData)
            {
                header('Location: index.php?expired');
                die;
            }
            $sid = $_SESSION['sid'];
            $brandlist = $otapilib->GetBrandInfoListFrame(0,30);
            $brands = $brandlist['Content'];
			
			//Твйтл и дескприптион
			try {
				$SeoCatsRepository = new SeoCategoryRepository(new CMS());
				$brandData['seo'] = $SeoCatsRepository->getCategorySEO(@$_GET['id']);
			} catch (DBException $e) {
                Session::setError($e->getMessage(), 'DBError');                
			}
			//======================
            if ($otapilib->error_message == 'SessionExpired')
            {
                header('Location: index.php?expired');
                die;
            }
            list($hidden_recom, $hidden_popular) = $this->setHidden();
            include(TPL_DIR.'brands.php');
        } else {
            include(TPL_DIR.'login.php');
        } 
    }
    
    function hideAction(){
        if (Login::auth())
        {
            global $otapilib;
            
            $sid = $_SESSION['sid'];
            $xml = '<EditableBrandInfo><IsHidden>true</IsHidden></EditableBrandInfo>';
            
            $result = $otapilib->EditBrandInfo($sid, @$_GET['id'], $xml);
            
            if (!$result)
            {
                header('Location: index.php?expired');
                die;
            }
            if ($result && $_POST['ajax']){
                print json_encode(array('success' => true));
                @define('NO_DEBUG', 1);
            }
            else{
                header('Location: index.php?cmd=brands');
            }
        } elseif($_POST['ajax']) {
            print json_encode(array('success' => false, 'error' => LangAdmin::get('session_ended_with_the_administrator')));
            @define('NO_DEBUG', 1);
        } else {
            include(TPL_DIR.'login.php');
        } 
    }
    
    function showAction(){
        if (Login::auth())
        {
            global $otapilib;
            
            $sid = $_SESSION['sid'];
            $xml = '<EditableBrandInfo><IsHidden>false</IsHidden></EditableBrandInfo>';
            
            $result = $otapilib->EditBrandInfo($sid, @$_GET['id'], $xml);
            
            if (!$result)
            {
                header('Location: index.php?expired');
                die;
            }
            if ($result && $_POST['ajax']){
                print json_encode(array('success' => true));
                @define('NO_DEBUG', 1);
            }
            else{
                header('Location: index.php?cmd=brands');
            }
        } elseif($_POST['ajax']) {
            print json_encode(array('success' => false, 'error' => LangAdmin::get('session_ended_with_the_administrator')));
            @define('NO_DEBUG', 1);
        } else {
            include(TPL_DIR.'login.php');
        } 
    }
    
    function delAction(){
        if (Login::auth())
        {
            global $otapilib;
            
            $sid = $_SESSION['sid'];
            
            $itemid = @$_GET['id'];
            $result = $otapilib->RemoveBrandInfo($sid, $itemid);

            if (!$result)
            {
                header('Location: index.php?expired');
                die;
            }

            if (isset($_GET['return'])) {
                header('Location: ' . $_GET['return']);
            }
            else {
                header('Location: index.php?cmd=brands&sid=');
            }

        } else {
            include(TPL_DIR.'login.php');
        } 
    }
    
    
}