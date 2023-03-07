<?php
class SiteConfiguration extends GeneralUtil{
    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = 'index';
    protected $_template_path = 'site_config/';

    public function __construct(){
        parent::__construct();
    }

    public function defaultAction(){
        if(!$this->checkAuth()) return false;

        if(!$this->cmsStatus) return false;
        $this->cms->checkTable('site_config');
        $this->tpl->assign('siteConfig', $this->cms->getSiteConfig());

        $this->cms->checkTable('site_langs');
        $languages = (CMS::IsFeatureEnabled('MultipleLanguages')) ? $this->cms->getLanguages() : Array();
        $current_lang = $this->setActiveLang();

        $this->tpl->assign('langs', $languages);
        $this->tpl->assign('current_lang', $current_lang);
        $this->tpl->assign('availLangsForNotification', $languages);
        print $this->fetchTemplate();
    }

    private function setActiveLang() {
        if (isset($_GET['lang'])) {
            $_SESSION['translate_lang'] = $_GET['lang'];
        } else {
            return '';
        }
        return $_SESSION['translate_lang'];
    }

    public function saveConfigurationAction(){
		global $otapilib;
		$sid = $_SESSION['sid'];
        if(!$this->checkAuth()) return false;
        if(!$this->cmsStatus)
            return $this->setErrorAndRedirect(LangAdmin::get('error_connecting_to_database'),
                'index.php?cmd=siteConfiguration');

        $xml = simplexml_load_file(dirname(dirname(__FILE__)) .
        	'/templates/' . $this->_template_path . 'parameters.xml');

        foreach($xml as $p) {
        	if (!isset($p['max'])) continue;
        	if (isset($_POST[(string)$p['name']])) {
        		if ((int)$_POST[(string)$p['name']] > (int)$p['max']) {
        			$_POST[(string)$p['name']] = (int)$p['max'];
        		}
        	}
        }
        
        $this->cms->saveSiteConfig($_POST);
		//проверям на запись статистики В сервисы
		//items_with_popular
		//items_with_last
		if ((General::getNumConfigValue('items_with_popular')==0) and (General::getNumConfigValue('items_with_last')==0)) {			
			$xml = "<StatisticsSettingsUpdateData IsNeedCollect=\"false\">";
			$otapilib->UpdateStatisticsSettings($sid,$xml);			
		} else {			
			$xml = "<StatisticsSettingsUpdateData IsNeedCollect=\"true\">";
			$otapilib->UpdateStatisticsSettings($sid,$xml);	
		}		
		
        header('Location: index.php?cmd=siteConfiguration');
    }
	
	public function generateSiteMapAction(){
		if(!$this->checkAuth()) return false;
		
		$url_base = 'http://'.$_SERVER['SERVER_NAME'].'/';
		
		$pages = $this->getPages();
		$categories = $this->getCategories();
		
		// Generate site map 
		$xml = new SimpleXMLElement('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" />');
		
		if($pages) foreach($pages as $p){
			$el = $xml->addChild('url');
			$el->addChild('loc', $url_base.UrlGenerator::generateContentUrl($p['alias']) );
			
			if($p['subpages']) foreach($p['subpages'] as $sp){
				$el = $xml->addChild('url');
				$el->addChild('loc', $url_base.UrlGenerator::generateContentUrl($sp['alias']) );
            }
		}
		
		if($categories) foreach($categories as $c){
			$el = $xml->addChild('url');
			$el->addChild('loc', str_replace('&', '&amp;', 
												$url_base.UrlGenerator::generateSubcategoryUrl(array_merge(array('clear'=> true), $c))
											) 
						  );
			
			if($c['children']) foreach($c['children'] as $sc){
				$el = $xml->addChild('url');
				$el->addChild('loc', str_replace('&', '&amp;', 
													$url_base.UrlGenerator::generateSubcategoryUrl( array_merge(array('clear'=> true), $sc))
												) 
							  );
			}
		}

        $newXML = Plugins::invokeEvent('onGenerateSiteMap', array('xml' => $xml, 'cms' => $this->cms));

		$result = $newXML ? $newXML->asXML(CFG_APP_ROOT.'/sitemap.xml') : $xml->asXML(CFG_APP_ROOT.'/sitemap.xml');
        print $result ? 'ok' : LangAdmin::get('sitemap_error');
        die();
    }
	
	private function getCategories(){
        global $otapilib;

        $otapilib->curl_timeout = 600;
        $cats = $otapilib->GetTwoLevelRootCategoryInfoList();
        if(in_array('Seo2', General::$enabledFeatures)){
			try {
            	$SeoCatsRepository = new SeoCategoryRepository(new CMS());
            	if(is_array($cats))
            	foreach($cats as &$c){
                	$c['alias'] = $SeoCatsRepository->getCategoryAlias($c['Id'], $c['Name']);
            	}
			} catch (DBException $e) {
                Session::setError($e->getMessage(), 'DBError');                
			}
			
        }
        
        $treeCats = array();
        if ($cats) foreach($cats as $c){
            if(!$c['ParentId']){
                $treeCats[$c['Id']] = array_merge($c, array('children' => array()));
            }
            else{
                $treeCats[$c['ParentId']]['children'][] = $c;
            }
        }
		else {
			return false;
		}
		
		return $treeCats;
	}
	
	private function getPages(){
        $cms = new CMS();
        $menu = false;
        if($cms->Check()){
            $top_menu = $cms->getBlock('top_menu_'.$_SESSION['active_lang']);
            if($top_menu){
                $top_menu_full = json_decode($top_menu);
				
                $menu = array();
                foreach($top_menu_full as $m){
                    $isContentPage = is_numeric($m);
                    $page = $isContentPage ? $cms->GetPageByID($m) : array('alias'=>$m,'title'=>Lang::get($m));
                    if($page)
                        $menu[] = $page;
                }
            }
			
			$left_menu = $cms->getBlock('left_menu_'.$_SESSION['active_lang']);
			if($left_menu){
                $left_menu_full = json_decode($left_menu);
				
				if(!is_array($menu)) 
					$menu = array();
					
                foreach($left_menu_full as $m){
                    $isContentPage = is_numeric($m);
                    $page = $isContentPage ? $cms->GetPageByID($m) : array('alias'=>$m,'title'=>Lang::get($m));
                    if($page)
                        $menu[] = $page;
                }
            }
        }
		
		if(is_array($menu)) foreach($menu as $key=>$m){
			$menu[$key]['subpages'] = false;
			if(isset($m['id'])){
				$menu[$key]['subpages'] = $this->getChildrenPages($m['id']);
			}
		}
		
        return $menu;
    }
	
	private function getChildrenPages($id){
		$cms = new CMS();
		$cms->Check();
		$cms->checkTable('site_pages_parents');
		$q = mysql_query("SELECT * FROM `site_pages_parents` WHERE `parent_id`=$id");
		
		if(!$q) return false;	
		
		$children = array();
		while($row = mysql_fetch_assoc($q)){
			$children[] = $cms->GetPageByID($row['page_id']);
		}
		return $children;
	}
}
