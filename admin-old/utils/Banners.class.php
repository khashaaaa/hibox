<?php

class Banners { 
    
    function defaultAction()
    {
        global $otapilib;
        if (Login::auth())
        {
			try {
            	$BannersRepository = new BannerRepository(new CMS());
				$banners = $BannersRepository->GetBanners();
			} catch (DBException $e) {
            	Session::setError($e->getMessage(), 'DBError');                
        	}
			
            $sid = $_SESSION['sid'];
            $data = $otapilib->GetWebUISettings($sid);
            include(TPL_DIR.'banners.php');
        } else {
            include(TPL_DIR.'login.php');
        } 
    }
    
    //
    function addAction(){
        require 'Upload2.php';

        if (Login::auth())
        {
            
            $BannersRepository = new BannerRepository(new CMS());
			
            $allowedExtensions = array("jpeg", "jpg", "gif", "png", "bmp", "swf");
            $sizeLimit = 20 * 1024 * 1024;

            $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
            $result = $uploader->handleUpload(dirname(dirname(dirname(__FILE__))).'/brands_uploads/');

            if (isset($result['success'])) {	
				try {
					$BannersRepository->AddBanner($_POST,$result['url']);				
				} catch (DBException $e) {
            		Session::setError($e->getMessage(), 'DBError');                
        		}
            } else {
                Session::setError(LangAdmin::get('failed_load_file'));
            }
            
            header('Location: ?cmd=banners');
            //include(TPL_DIR.'banners.php');
        } else {
            include(TPL_DIR.'login.php');
        } 
    }
	
	function EditAction(){
        require 'Upload2.php';

        if (Login::auth())
        {
            
            $BannersRepository = new BannerRepository(new CMS());
			$allowedExtensions = array("jpeg", "jpg", "gif", "png", "bmp", "swf");
            $sizeLimit = 20 * 1024 * 1024;

            $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
            $result = $uploader->handleUpload(dirname(dirname(dirname(__FILE__))).'/brands_uploads/');
			            
            if ($_POST['PictureUrl']) {	
				try {
					 
					$BannersRepository->EditBanner($_POST,isset($result['url']) ? $result['url'] : $_POST['PictureUrl']);				
				} catch (DBException $e) {
            		Session::setError($e->getMessage(), 'DBError');                
        		}
            } else {               
				Session::setError(LangAdmin::get('failed_load_file')); 
            }
            
            header('Location: ?cmd=banners');
            //include(TPL_DIR.'banners.php');
        } else {
            include(TPL_DIR.'login.php');
        } 
    }
    
    //
    function delAction(){
        if (Login::auth())
        {
            $id = @$_GET['id'];
			try {
            	$BannersRepository = new BannerRepository(new CMS());
				$BannersRepository->DelBanner($id);
			} catch (DBException $e) {
            		Session::setError($e->getMessage(), 'DBError');                
        	}
            
            header('Location: ' . $_GET['return']);

            include(TPL_DIR.'banners.php');
        } else {
            include(TPL_DIR.'login.php');
        } 
    }
    
    function saveorderAction(){
        if (Login::auth())
        {
            $ids = @$_POST['ids'];
            $ids = explode(';', $ids);
			$BannersRepository = new BannerRepository(new CMS());
            
           		
            foreach($ids as $num => $id) {
				if ($id=='') break;
				try {            	
					$BannersRepository->UpdateBanner($id,$num);					
				} catch (DBException $e) {
            		Session::setError($e->getMessage(), 'DBError');                
        		}			
			}

            header('Location: ' . $_POST['return']);

            include(TPL_DIR.'banners.php');
        } else {
            include(TPL_DIR.'login.php');
        } 
    }
    
    
   
    
}