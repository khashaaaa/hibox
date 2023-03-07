<?php

class Category extends GeneralUtil
{

    function defaultAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = @$_SESSION['sid'];
            $instanceOptionsInfo = $otapilib->GetInstanceOptionsInfo($sid);
            $useMetrologist = false;
            foreach($instanceOptionsInfo['Features'] as $v){
                if($v['Name'] == 'Metrologist'){
                    $useMetrologist = true;
                    break;
                }
            }
            $webui = $otapilib->GetWebUISettings($sid);
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }
			if (!isset($_SESSION['CatShowMode'])) { 
				if (isset($_GET['showmode'])) {
					$CatShowMode = $_GET['showmode'];              
            	} else {
                	$CatShowMode = 'false';
            	}
				$_SESSION['CatShowMode'] = $CatShowMode;
			}
			if (isset($_GET['showmode'])) 
				$_SESSION['CatShowMode'] = $_GET['showmode']; 
				
            $cats = $otapilib->GetEditableCategorySubcategories($sid,0,$_SESSION['CatShowMode']);			
			
            //Получаем список удаленных
			
            $EmptyCats = $otapilib->SearchDeletedCategoriesIds($sid);
			try {
            	$SeoCatsRepository = new SeoCategoryRepository(new CMS());
				if (is_array($cats))
            	foreach ($cats as $k => &$c) {
                	$c['alias'] = $SeoCatsRepository->getCategoryAlias($c['Id']);
                	$c['seo'] = $SeoCatsRepository->getCategorySEO($c['Id']);
				
            	}
			} catch (DBException $e) {
                Session::setError($e->getMessage(), 'DBError');                
			}
			
			
            if (isset($_COOKIE['HiddenState'])) {
                $hiddenstat = @$_COOKIE['HiddenState'];
            } else {
                $hiddenstat = 1;
            }

            $usedLanguages = $this->getActiveLanguages();

            include(TPL_DIR . 'categories.php');
        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    function savestatAction()
    {
        Cookie::set('HiddenState', @$_GET['stat'], time() + 3600 * 24 * 30);
    }

	function hidedeletedcatsAction()
    {
        global $otapilib;
        @define('NO_DEBUG', 'true');
        //$sid = @$_GET['sid'];
        $sid = $_SESSION['sid'];
        $result = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired') {
            print 'RELOGIN';
            die;
        }
		
        $otapilib->HideDeletedCategoriesWithoutChildren($sid);				
	    header('Location: index.php?cmd=category&sid='.$sid); 		
       
    }

    function getAction()
    {
        global $otapilib;
        @define('NO_DEBUG', 'true');
        //$sid = @$_GET['sid'];
        $sid = $_SESSION['sid'];
        $result = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired') {
            print 'RELOGIN';
            die;
        }

        if (!isset($_GET['catid'])) {
			$cats = $otapilib->GetEditableCategorySubcategories($sid,0,$_SESSION['CatShowMode']);
            //$cats = $otapilib->GetRootCategoryInfoList();
        } else {
			$cats = $otapilib->GetEditableCategorySubcategories($sid,$_GET['catid'],$_SESSION['CatShowMode']);
            //$cats = $otapilib->GetCategorySubcategoryInfoList($_GET['catid']);
        }


        
		
		if (is_array($cats))
			if(in_array('Seo2', General::$enabledFeatures)){
				try {
					$SeoCatsRepository = new SeoCategoryRepository(new CMS());
        			foreach ($cats as $k => &$c) {
            			$c['alias'] = $SeoCatsRepository->getCategoryAlias($c['Id']);
            			$c['seo'] = $SeoCatsRepository->getCategorySEO($c['Id']);
        			}
				} catch (DBException $e) {
                	Session::setError($e->getMessage(), 'DBError');                
				}
				
			}
        $parentid = $_GET['catid'];
        include(TPL_DIR . 'categotylist.php');
    }

    function getforpricingAction()
    {
        global $otapilib;		
        @define('NO_DEBUG', 'true');
        $sid = $_SESSION['sid'];
        $result = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired') {
            print 'RELOGIN';
            die;
        }
		
        if (!isset($_GET['catid'])) {
            $cats = $otapilib->GetRootCategoryInfoList();
        } else {
            $cats = $otapilib->GetCategorySubcategoryInfoList($_GET['catid']);
        }
        $parentid = $_GET['catid'];
        include(TPL_DIR . 'pricing/categotylist.php');
    }

    function gethintAction()
    {
        global $otapilib;
        @define('NO_DEBUG', true);
        $sid = $_SESSION['sid'];
        $hint = $_REQUEST['term'];
        $cats = $otapilib->FindHintCategoryInfoList($hint);
        $data = '[ ';
		
        foreach ($cats as $cat) {
            $path = '';
            if (isset($cat['path'])) {
                foreach ($cat['path'] as $pitem) {
                    $path .= $pitem['name'] . ' → ';
                }
                //$path = substr($path, 0, -5);
            }
            $data .= '{ "id": "' . $cat['id'] . '", "label": "' . $cat['name'] . '", "value": "' . $cat['id'] . '" , "path": "' . $path . '" },';
        }
        $data = substr($data, 0, -1) . ' ]';
        print $data;
        die;
    }

    function visibleAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $cid = $_GET['cid'];
            //$sid  = $_GET['sid'];
            $sid = $_SESSION['sid'];
            $pcid = $_GET['pcid'];
            if ($_GET['ishidden'] == 'false')
                $categorySettings = $cid . '-0';
            else
                $categorySettings = $cid . '-1';
            $data = $otapilib->EditCategoriesVisible($categorySettings, $sid);
            if ($otapilib->error_message == 'SessionExpired') {
                print 'RELOGIN';
                die;
            }
            if ($data === false) {
                print '-1';
            } elseif ($data == 'Ok') {
                print '1';
                @define('NO_DEBUG', true);
            }
        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    function removeAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $cid = $_GET['cid'];
            $sid = $_SESSION['sid'];
            $pcid = $_GET['pcid'];
            $data = $otapilib->RemoveCategory($sid, $cid);
            if ($otapilib->error_message == 'SessionExpired') {
                print 'RELOGIN';
                die;
            }
            //

            if ($pcid == '' || $pcid == '0') {
                $cats = $otapilib->GetEditableCategorySubcategories($sid,0,$_SESSION['CatShowMode']);
            } else {
                $cats = $otapilib->GetEditableCategorySubcategories($sid,$pcid,$_SESSION['CatShowMode']);  
            }
			try {
				$SeoCatsRepository = new SeoCategoryRepository(new CMS());            			
				if (is_array($cats))
            	foreach ($cats as $k => &$c) {
                	$c['alias'] = $SeoCatsRepository->getCategoryAlias($c['Id']);
            	}
			} catch (DBException $e) {
                Session::setError($e->getMessage(), 'DBError');                
			}

            $parentid = $pcid;
            include(TPL_DIR . 'categotylist.php');
            @define('NO_DEBUG', true);
        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    private function generateXML($weight = false){
        if($weight !== false){
            $xml = new SimpleXMLElement('<EditableCategoryInfo></EditableCategoryInfo>');
            if($weight === ''){
                $xml->addChild('ResetApproxWeight', 'true');
            }
            else{
                $xml->addChild('ApproxWeight', $weight);
            }
            return $xml->asXML();
        }
        return '';
    }

    function saveAction()
    {
        if (Login::auth()) {
            $this->rrmdir(dirname(__FILE__) . '/../../cache/', false);

            global $otapilib;
            $cid = @$_GET['cid'];
            $name = @$_GET['name'];
            $sid = $_SESSION['sid'];
            $pcid = @$_GET['pcid'];
            $parentid = @$_GET['parentid'];
            $parentname = @$_GET['parentid2'];
            $categoryId = @$_GET['categoryId'];
            $approximateWeight = isset($_GET['approximate_weight']) ? $_GET['approximate_weight'] : false;
            if ($approximateWeight) $approximateWeight = str_replace(',', '.', $approximateWeight);
			$alias = @$_GET['alias'];
			
			try{				
				$SeoCatsRepository = new SeoCategoryRepository(new CMS());		
				if(in_array('Seo2', General::$enabledFeatures)){
                    $SeoCatsRepository->setCategoryAlias($cid, $alias);
                    $SeoCatsRepository->setCategorySEO($_GET);	
				}
			} catch (DBException $e) {
                Session::setError($e->getMessage(), 'DBError');                
			}
            $otapilib->EditCategoryNameByLanguage($sid, $cid, $name);
            if ($otapilib->error_message == 'SessionExpired') {
                print 'RELOGIN';
                die;
            }
            $xml = $this->generateXML($approximateWeight);
            if($xml){
                $result = $otapilib->EditCategoryInfo($sid, $cid, $xml);
                if($result === false){
                    print '<span style="color:red">'.$otapilib->error_message.'</span><br />';
                }
            }

            $otapilib->EditCategoryExternalId($cid, $categoryId, $sid);
            print $otapilib->error_message;

            settype($parentid, 'string');
            settype($parentname, 'string');
            if ($parentid === '' || $parentname === '0') $parentid = $parentname;
            if ($parentid !== '') {
                $otapilib->EditCategoryParent($sid, $cid, $parentid);
                print 'REFRESH';
                die;
            }

            if ($pcid == '' || $pcid == '0') {
                $cats = $otapilib->GetEditableCategorySubcategories($sid,0,$_SESSION['CatShowMode']);
            } else {
                $cats = $otapilib->GetEditableCategorySubcategories($sid,$pcid,$_SESSION['CatShowMode']); 
            }

			if (is_array($cats))
			try {
            	foreach ($cats as $k => &$c) {
                	$c['alias'] = $SeoCatsRepository->getCategoryAlias($c['Id']);
                	$c['seo'] = $SeoCatsRepository->getCategorySEO($c['Id']);
                	 
            	}
			} catch (DBException $e) {
                Session::setError($e->getMessage(), 'DBError');                
			}
            $parentid = $pcid;
            include(TPL_DIR . 'categotylist.php');
            @define('NO_DEBUG', true);
        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    function addformAction()
    {
        if (Login::auth()) {
            //$sid = @$_GET['sid'];
            $sid = $_SESSION['sid'];
            $pcid = @$_GET['pcid'];
            include(TPL_DIR . 'category.php');
        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    function addAction()
    {
        if (Login::auth()) {
            $this->rrmdir(dirname(__FILE__) . '/../../cache/', false);
            global $otapilib;
            $cid = $_GET['cid'];
            //$sid  = $_GET['sid'];
            $sid = $_SESSION['sid'];
            $name = $_POST['name'];
            $parentid = @$_POST['parentid'];
            $categoryId = @$_POST['categoryId'];
            $data = $otapilib->AddCategoryByLanguage($sid, $name, $parentid, $categoryId);
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                //print 'RELOGIN';
                die;
            }
            if ($data === true) header('Location: ?sid=' . $sid . '&cmd=category');
            //header('Location: ?sid='.$sid.'&cmd=category');
            die;
        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    function add2Action()
    {
        if (Login::auth()) {
            $this->rrmdir(dirname(__FILE__) . '/../../cache/', false);
            global $otapilib;
            $pcid = $_GET['pcid'];
            $sid = $_SESSION['sid'];
            $name = $_GET['name'];
            $parentid = @$_GET['parentid'];
            $categoryId = @$_GET['categoryId'];
            $approximateWeight = isset($_GET['approximate_weight']) ? $_GET['approximate_weight'] : false;
            if ($approximateWeight) $approximateWeight = str_replace(',', '.', $approximateWeight);

            //Added by Gevorg
            //$sxml = $this->parseSearchUrl();
            //echo 'Parsed XML is <hr>'.$sxml.'<hr>';
            // ETC not ready yet,,,

            $data = $otapilib->AddCategoryByLanguage($sid, $name, $parentid, $categoryId);
            if ($otapilib->error_message == 'SessionExpired') {
                print 'RELOGIN';
                die;
            }
            if ($data) {
                $xml = $this->generateXML($approximateWeight);
                $cid = (string)$data;
                if($xml){
                    $result = $otapilib->EditCategoryInfo($sid, $cid, $xml);
                    if($result === false){
                        print '<span style="color:red">'.$otapilib->error_message.'</span><br />';
                    }
                }
            }

            if ($pcid == '' || $pcid == '0' || $pcid == 'undefined') {
                $cats = $otapilib->GetEditableCategorySubcategories($sid,0,$_SESSION['CatShowMode']);
            } else {
                $cats = $otapilib->GetEditableCategorySubcategories($sid,$pcid,$_SESSION['CatShowMode']);  
            }
            if ($cats === false) $cats = array();
            $parentid = $_GET['pcid'];
            include(TPL_DIR . 'categotylist.php');
            @define('NO_DEBUG', true);
        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    /*-----------------------------------------------------------
            START OF URL TO XML PARSING METHODS
    ------------------------------------------------------------*/

    // Parse XML from obtained url
    private function parseSearchUrl($url)
    {

        $parse_url = parse_url($url);
        $q_string = $parse_url['query'];
        parse_str($q_string, $search_params);

        $xmlParams = new SimpleXMLElement('<SearchItemsParameters></SearchItemsParameters>');

        // Loading Predifined parameters
        $xmlSearchConfig = simplexml_load_file(CFG_APP_ROOT.'/config/request2xml.search.xml');
        foreach($xmlSearchConfig->predefined_paramters->parameter as $c)
            $xmlParams->addChild((string)$c['name'], (string)$c[0]);

        foreach($xmlSearchConfig->parameter as $c)
            self::getXmlParameter($search_params,$c->children(),(string)$c['name'],$xmlParams);

        self::prepareFiltersXml($search_params, $xmlParams);

        //die($xmlParams->asXML());

        return $xmlParams->asXML();
    }

    // Request type is always GET ???
    public static function getXmlParameter($params, $requestKeys, $xmlKey, &$xmlElement){

        $value = @self::getArrayValueByKeys($params, $requestKeys);
        if($value)
            $xmlElement->addChild($xmlKey, htmlspecialchars($value));

    }

    public static function getArrayValueByKeys($array, $keys){
        $tmp = $array;
        foreach($keys->request as $k){
            $tmp = $tmp[(string)$k];
        }
        return $tmp;
    }

    public static function prepareFiltersXml($search_params, &$xmlElement){
        if (isset($search_params['filters'])) {
            $configuratorsXml = $xmlElement->addChild('Configurators');
            foreach ($search_params['filters'] as $pid => $vid) {
                if ($vid && $pid!='StuffStatus'){
                    $el = $configuratorsXml->addChild('Configurator');
                    $el->addAttribute('Pid', $pid);
                    $el->addAttribute('Vid', $vid);
                }
                elseif($pid=='StuffStatus' && $vid){
                    $xmlElement->addChild('StuffStatus', $vid);
                }
            }
        }
    }

    /*-----------------------------------------------------------
            END OF URL TO XML PARSING METHODS
    ------------------------------------------------------------*/

    function orderAction()
    {
        if (Login::auth()) {
            $this->rrmdir(dirname(__FILE__) . '/../../cache/', false);
            global $otapilib;
            //$sid = $_GET['sid'];
            $sid = $_SESSION['sid'];
            $cid = $_GET['cid'];
            $pcid = $_GET['pcid'];

            $i = isset($_GET['down']) ? $_GET['i'] + 1 : $_GET['i'] - 1;

            $data = $otapilib->EditOrderOfCategory($i, $cid, $sid);
            if ($otapilib->error_message == 'SessionExpired') {
                //print 'RELOGIN';
                die;
            }

            if ($pcid == '' || $pcid == '0') {
                $cats = $otapilib->GetEditableCategorySubcategories($sid,0,$_SESSION['CatShowMode']);
            } else {
                $cats = $otapilib->GetEditableCategorySubcategories($sid,$pcid,$_SESSION['CatShowMode']);
            }

            if ($cats === false) $cats = array();
            $parentid = $_GET['pcid'];
            include(TPL_DIR . 'categotylist.php');
            @define('NO_DEBUG', true);
        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    function order2Action()
    {
        if (Login::auth()) {
            $this->rrmdir(dirname(__FILE__) . '/../../cache/', false);
            global $otapilib;
            $sid = $_SESSION['sid'];
            $cid = @$_GET['cid'];
            $pcid = @$_GET['pcid'];
            $pos = @$_GET['pos'];
            $pcid = str_replace('incat', '', $pcid);
            $cid = str_replace('cat', '', $cid);

            $data = $otapilib->EditOrderOfCategory($pos, $cid, $sid);
            if ($otapilib->error_message == 'SessionExpired') {
                print 'RELOGIN';
                die;
            }
            die;
        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    function exportAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = $_SESSION['sid'];
            $data = $otapilib->ExportStructureByLanguage($sid);
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }
            if ($data) {
                header('Content-Type: text/plain; charset:utf-8;');
                header('Content-Disposition: attachment; filename="categories.txt"');
                echo base64_decode($data);
                echo "\r\n";
            }
            else{
                $_SESSION['error'] = 'empty_catalog';
                header('Location: index.php?cmd=category');
            }

            @define('NO_DEBUG', true);
        } else {
            include(TPL_DIR . 'login.php');
        }
    }
	
//=====================================================================	
	function exportToTranslateAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = $_SESSION['sid'];
            $data = $otapilib->ExportStructureByLanguage($sid);
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }
            if ($data) {
				$info = array();
				$info[] = 'Original name;New name;';
                $data = base64_decode($data);
				$data = explode("\r\n", $data);				
                //Парсим по названиям
				foreach ($data as $line) {
					$tmp = explode("|", $line);	
					$tmp = array_diff($tmp, array(''));
					$cat_name = current($tmp);
					$info[] = $cat_name.';;'; 
					reset($tmp);
				}
				$info = array_unique($info);
				$export = '';
				foreach ($info as $line) {
					$export.=$line; $export.="\r\n";
				}
				//$pieces = explode("|", $data);
				header('Content-Type: text/plain; charset:cp-1251;');
                header('Content-Disposition: attachment; filename="categories.csv"');                
				echo $export;
				
            }
            else{
                $_SESSION['error'] = 'empty_catalog';
                header('Location: index.php?cmd=category');
            }

            @define('NO_DEBUG', true);
			die();
        } else {
            include(TPL_DIR . 'login.php');
        }
    }
	
	function importFileformAction()
    {
        if (Login::auth()) {
            include(TPL_DIR . 'importfileform.php');

        } else {
            include(TPL_DIR . 'login.php');
        }
    }
	
	function importToTranslateAction()
    {
        if (Login::auth()) {
            $this->rrmdir(dirname(__FILE__) . '/../../cache/', false);

            global $otapilib;
            $sid = $_SESSION['sid'];
			//Готовим закачаный
            $content = file_get_contents($_FILES['userfile']['tmp_name']);			
			$data = explode("\r\n", $content);
			
			//Получаем оригинал для сверки
			$original = $otapilib->ExportStructureByLanguage($sid);
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }
			if ($original) {
				$original = base64_decode($original);
				foreach ($data as $line) {
					$tmp = explode(";", $line);					
					if (isset($tmp[1]) and ($tmp[1]<>'')) $original = str_replace($tmp[0],$tmp[1],$original);
				}
			}
						
            $sorce = base64_encode($original);

            $data = $otapilib->ImportStructureByLanguage($sid, $sorce);
            $message = '&success='.LangAdmin::get('catalog_imported');
            if (!$data) {
                $message = '&error='.$otapilib->error_message;
            }
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }
            $url = 'index.php?cmd=category'.$message;
            header("Location: {$url}");
            die;
        } else {
            include(TPL_DIR . 'login.php');
        }
    }

//=====================================================================	
    function importformAction()
    {
        if (Login::auth()) {
            include(TPL_DIR . 'importform.php');

        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    function importAction()
    {
        if (Login::auth()) {
            $this->rrmdir(dirname(__FILE__) . '/../../cache/', false);

            global $otapilib;
            $sid = $_SESSION['sid'];
            $content = file_get_contents($_FILES['userfile']['tmp_name']);
            $sorce = base64_encode($content);

            $data = $otapilib->ImportStructureByLanguage($sid, $sorce);
            $message = '&success='.LangAdmin::get('catalog_imported');
            if (!$data) {
                $message = '&error='.$otapilib->error_message;
            }
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }
            $url = 'index.php?cmd=category'.$message;
            header("Location: {$url}");
            die;
        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    function rrmdir($dir, $removeDir = true)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") $this->rrmdir($dir . "/" . $object); else unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            if ($removeDir)
                rmdir($dir);
        }
    }

    public function getSeoTextAction(){
        $res = General::getSeoText($_POST['category_id']);
        print $res;
        @define('NO_DEBUG', 1);
        return;
    }

    public function saveSeoTextAction(){
        $this->linkCms();
        $id = mysql_real_escape_string($_POST['category_id']);
        $this->cms->query('DELETE FROM `site_categories_seo_texts` WHERE `category_id`="'.$id.'"');
        $this->cms->query('INSERT INTO `site_categories_seo_texts` SET `category_id`="'.$id.'", `text`="'.
            mysql_real_escape_string(stripslashes($_POST['text'])).'"');
        @define('NO_DEBUG', 1);
        return;
    }

    // получить заполненный шаблон с фильтрами для категории
    public function getSearchSelectAction()
    {
        global $otapilib;
        if (Login::auth()) {
            $category_id = mysql_real_escape_string($_POST['category_id']);
            $searchprops = $otapilib->GetCategorySearchProperties($category_id);
            @define('NO_DEBUG', 1);

            include(TPL_DIR . 'category/tpl_seacrh_filter_all.php');
            return;
        }
    }
    // изменить перевод для фильтра категории по параметрам:
    // $_POST['category_id'] - id изменяемого фильтра или свойства
    // $_POST['text'] - пользовательский перевод
    // $_POST['type'] - ItemPropertyName (наименование фильтра) или ItemPropertyValueName (свойства фильтра)
    public function updateSearchFilterAction()
    {
        global $otapilib;
        if (Login::auth()) {
            $category_id = (string)mysql_real_escape_string($_POST['category_id']);
            $text = (string)mysql_real_escape_string($_POST['text']);
            $type = mysql_real_escape_string($_POST['type']);

            $sid = (string)$_SESSION['sid'];
            $lang = (string)$_SESSION['active_lang'];

            switch ($type) {
                case 'ItemPropertyName': {
                    $key = (string)(CFG_SERVICE_INSTANCEKEY . ':taobao:ItemProperty:Name');
                    $otapilib->EditTranslateByKey($sid, $lang, $text, $key, $category_id);					
                } break;
                case 'ItemPropertyValueName': {
                    $key = (string)(CFG_SERVICE_INSTANCEKEY . ':taobao:ItemPropertyValue:Name');
                    $otapilib->EditTranslateByKey($sid, $lang, $text, $key, $category_id);					
                } break;

                default:
                    break;
            }
            @define('NO_DEBUG', 1);
// echo "'" . $sid . "', '" . $text . "', '" . $key . "', '" . $category_id . "', '" . $lang . "'";

            echo '1'; die();
        }
    }

}