<?php
class Sitemap extends GenerateBlock
{
    protected $_cache = CFG_CACHED; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'sitemap'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';

    public function __construct() {
        parent::__construct(true);
    }

    private function getAllPageInfoByLang($lang)
    {
		$cRep = new ContentRepository($this->cms);
        $sql = "SELECT p.id as `id`, pp.parent_id as `parent_id`
            FROM  `pages` p
            LEFT JOIN `site_pages_parents` pp ON ( p.id = pp.page_id )
            LEFT JOIN `site_pages_langs` pl ON ( p.id = pl.page_id )
            LEFT JOIN `site_langs` sl ON ( sl.id = pl.lang_id )
            WHERE sl.lang_code =  '" . $lang . "' ORDER BY `parent_id`, `menu_order`";
        $pages = $this->cms->queryMakeArray($sql, array('pages', 'site_pages_parents', 'site_pages_langs_data', 'site_pages_langs'));

        foreach ($pages as &$page) {
            $pageInfo = $cRep->GetPageByID($page['id']);
            $page = array_merge($page, $pageInfo);
        }

        return $pages;
    }

    private function getPages()
    {
        $lang = Session::getActiveLang();
        $pagesSite = $this->getAllPageInfoByLang($lang);

        $pageView = array();
        foreach ($pagesSite as $page) {
            if ($page['is_service']) {
                if ($page['alias'] == 'digest') {
                    $this->getDigestPages();
                }
                continue;
            }
            if (! $page['is_index']) {
                continue;
            }
            if ($page['parent_id']) {
                $pageView[$page['parent_id']]['subpages'][] = $page;
            } else {
                $pageView[$page['id']] = $page;
            }
        }

        $this->tpl->assign('pages', $pageView);
    }

    private function getDigestPages()
    {
        $lang = Session::getActiveLang();

        $sql = 'SELECT d.id, l.lang_code, d.title, d.alias, dc.id cat_id, dc.title cat_title, dc.alias cat_alias 
                FROM digest d
                LEFT JOIN site_digest_categories dc ON d.category_id = dc.id
                INNER JOIN site_digest_langs dl ON d.id=dl.post_id
                INNER JOIN site_langs l ON dl.lang_id=l.id
                WHERE l.lang_code= "' . $lang . '" ORDER BY dc.title, d.title';
        $r = $this->cms->query($sql);

        $pages = $digest = array();
        if ($r && mysqli_num_rows($r)) {

            while ($row = mysqli_fetch_assoc($r)) {
                $pages[] = $row;
            }
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
        $this->tpl->assign('digest', $digest);
    }

    protected function setVars() {
        // Запрашивается список брен
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
		
		// Страницы добавленные пользователями
		$this->getPages();
		
		//Каталог в карту сайта
        $this->tpl->assign('treeCats', $treeCats);
		
    }

}
?>