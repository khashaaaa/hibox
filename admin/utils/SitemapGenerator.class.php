<?php

class SitemapGenerator
{
	protected $cms;
	protected $repository;

	function __construct()
	{
		$this->cms = new CMS();
		$this->repository =  new SitemapGeneratorRepository($this->cms);
	}
		
	public function generateSiteMap()
	{
		$pages = $this->getPages();
		$categories = $this->getCategories();
        $digest = $this->getDigestPages();

		$url_base = UrlGenerator::getProtocol() . '://'.$_SERVER['SERVER_NAME'];
		
		// Generate site map
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>' .
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" />');

		if($pages) foreach($pages as $page) {
			$el = $xml->addChild('url');
						
			$el->addChild('loc', $url_base.UrlGenerator::generateContentUrl($page['alias']) );

			if($page['subpages']) foreach($page['subpages'] as $childPage) {
				$el = $xml->addChild('url');
				$el->addChild('loc', $url_base.UrlGenerator::generateContentUrl($childPage['alias']) );
			}
		}

        foreach($digest as $d) {
            if (! empty($d['alias'])) {
                $el = $xml->addChild('url');
                $el->addChild('loc', str_replace('&', '&amp;',
                $url_base.UrlGenerator::generateDigestUrl('digest', $d['alias'])));
            }

            if($d['articles']) foreach($d['articles'] as $child) {
                $el = $xml->addChild('url');
                $el->addChild('loc', str_replace('&', '&amp;',
                $url_base.UrlGenerator::generatPostUrl('post', $child['id'], $child['alias'])));
            }
        }

		foreach($categories as $category) {
			$el = $xml->addChild('url');
			$el->addChild('loc', str_replace('&', '&amp;',
					$url_base.UrlGenerator::generateSubcategoryUrl($category)));

			if($category['children']) foreach($category['children'] as $child) {
				$el = $xml->addChild('url');
				$el->addChild('loc', str_replace('&', '&amp;',
						$url_base.UrlGenerator::generateSubcategoryUrl($child)));
			}
		}

		$newXML = Plugins::invokeEvent('onGenerateSiteMap', array('xml' => $xml, 'cms' => $this->cms));

        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($newXML ? $newXML->asXML() : $xml->asXML());
        $result = $domxml->save(CFG_APP_ROOT.'/sitemap.xml');

		return $result;
	}

	private function getPages()
	{
		$menu = array();
        $topMenu = $this->repository->getBlock('top_menu_'.Session::getActiveLang());
        if($topMenu){
            $topMenuFull = json_decode($topMenu);

            foreach($topMenuFull as $m){
                $isContentPage = is_numeric($m);
                $page = $isContentPage ? $this->repository->GetPageByID($m) : array('alias'=>$m,'title'=>Lang::get($m));
                if($page)
                    $menu[] = $page;
            }
        }

        $leftMenu = $this->repository->getBlock('left_menu_'.Session::getActiveLang());
        if($leftMenu){
            $leftMenuFull = json_decode($leftMenu);

            if(!is_array($menu))
                $menu = array();

            foreach($leftMenuFull as $m){
                $isContentPage = is_numeric($m);
                $page = $isContentPage ? $this->repository->GetPageByID($m) : array('alias'=>$m,'title'=>Lang::get($m));
                if($page)
                    $menu[] = $page;
            }
        }

		foreach($menu as $key=>$m) {
			$menu[$key]['subpages'] = false;
			if(isset($m['id'])){
				$menu[$key]['subpages'] = $this->getChildrenPages($m['id']);
			}
		}

		return $menu;
	}

	private function getChildrenPages($id)
	{
		$this->repository->Check();
		$this->repository->checkTable('site_pages_parents');
		$parents = $this->repository->getSitePagesParents($id);

		$children = array();
		foreach($parents as $parent) {
			$children[] = $this->repository->GetPageByID($parent['page_id']);
		}
		return $children;
	}

	private function getCategories()
	{
		global $otapilib;

		$otapilib->curl_timeout = 600;
        $otapilib->setErrorsAsExceptionsOn();
		$categories = $otapilib->GetTwoLevelRootCategoryInfoList();
		if(CMS::IsFeatureEnabled('Seo2')){
            $SeoCatsRepository = new SeoCategoryRepository($this->cms);
            foreach($categories as &$category){
                $category['alias'] = $SeoCatsRepository->getCategoryAlias($category['Id'], $category['Name']);
            }
		}

		$treeCats = array();
		if ($categories) foreach($categories as $category) {
			if(!$category['ParentId']) {
				$treeCats[$category['Id']] = array_merge($category, array('children' => array()));
			} else {
				$treeCats[$category['ParentId']]['children'][] = $category;
			}
		}

		return $treeCats;
	}

    private function getDigestPages()
    {
        $lang = Session::getActiveLang();

        $sql = 'SELECT d.id, l.lang_code, d.title, d.alias, dc.id cat_id, dc.title cat_title, dc.alias cat_alias FROM digest d
                LEFT JOIN site_digest_categories dc ON d.category_id = dc.id
                INNER JOIN site_digest_langs dl ON d.id=dl.post_id
                INNER JOIN site_langs l ON dl.lang_id=l.id
                WHERE l.lang_code= "' . $lang . '" ORDER BY dc.title, d.title';
        $r = $this->cms->query($sql);

        $digest = array();
        if ($r && mysqli_num_rows($r)) {
            $pages = array();
            while ($row = mysqli_fetch_assoc($r)) {
                $pages[] = $row;
            }

            foreach ($pages as $key => $page) {
                if (isset($digest[$page['cat_id']]))
                        continue;

                $articles = array();
                foreach ($pages as $pageTmp) {
                    if ($pageTmp['cat_id'] == $page['cat_id']) {
                            $articles[] = array('id' => $pageTmp['id'], 'title' => $pageTmp['title'], 'alias' => $pageTmp['alias']);
                    }
                }
                $digest[$page['cat_id']] = array(
                    'id' => $page['cat_id'],
                    'title' => $page['cat_title'],
                    'alias' => $page['cat_alias'],
                    'articles' => $articles
                );
            }
        }
        return $digest;
    }
}
