<?php

class MenuShortNew extends GenerateBlock
{
    protected $_cache = true;
    protected $_life_time = 3600;
    protected $_template = 'menushortnew';
    protected $_template_path = '/menu/';
    protected $_hash = '';
    protected $whRootParentId = 'wh-0';
    protected $yjpRootParentId = 'yjp-0';
    
    private $categories =array();

    public function __construct()
    {
        parent::__construct(true);
        $this->tpl->_caching_id = Session::get('active_lang').@$_SERVER['HTTP_HOST'];
    }

    protected function setVars()
    {
        if (isset($GLOBALS['menu_ajax'])){
            $this->assignLocalCategories();

            $this->otapilib->curl_timeout = 20;

            $menu_type = General::getConfigValue('menu_type', 1);

            if ($menu_type=='3') {
                $this->_template = 'menushortnew_v2';
                $rootCats = $this->otapilib->GetThreeLevelRootCategoryInfoList();
            } else {
                $rootCats = $this->otapilib->GetTwoLevelRootCategoryInfoList();
            }
            
            if(!$rootCats || $rootCats === false){
                $this->tpl->_caching = false;
                $this->_cache = false;
            }

            if (is_array($rootCats) && !CMS::IsFeatureEnabled('Warehouse')) {
                // ограничить вывод категории Пристрой
                $whRootCategory = array();
                foreach ($rootCats as $key => $category) {
                    if ($category['ParentId'] == $this->whRootParentId) {
                        $whRootCategory = $category;
                        unset($rootCats[$key]);
                        break;
                    }
                }
                if (! empty($whRootCategory)) {
                    foreach ($rootCats as $key => $category) {
                        if ($category['ParentId'] == $whRootCategory['id']) {
                            unset($rootCats[$key]);
                        }
                    }
                }
            }

            if (CMS::IsFeatureEnabled('Seo2')) {
                $this->addAliasesToCategories($rootCats);
            }

            if ($menu_type=='3') {
                $treeCats = $this->convertCategoriesArrayToThreeLevelsTree($rootCats);
                
            } else {
                $treeCats = $this->convertCategoriesArrayToTwoLevelsTree($rootCats);                
            }
            
            $whChild = array();
            $whId = 0;
            foreach ($treeCats as $key => $cat) {
                if ((! empty($cat['externalid'])) && ($cat['externalid'] == $this->whRootParentId)) {
                    if ($cat['children'] && empty($whChild)) {
                        $whChild = $cat['children'];
                        $whId = $cat['Id'];
                    }
                }
            }
            foreach ($treeCats as $key => &$cat) {
                if ((! empty($cat['externalid'])) && ($cat['externalid'] == $this->whRootParentId)) {                
                    if ($cat['Id'] != $whId) {
                        $cat['children'] = array_merge($cat['children'], $whChild);
                    }
                }
            }
            
            $this->tpl->assign('treeCats', $treeCats);

            $this->tpl->assign('count_lvl1', General::getConfigValue('menu_count_lvl1', 10));
            $this->tpl->assign('count_lvl2', General::getConfigValue('menu_count_lvl2', 4));
            $this->tpl->assign('menu_type', $menu_type);
        } else {
            //Не закэшированно и прогружаем через аякс
            $this->_cache = false;
            $this->tpl->assign('menu_ajax', '1');
        }
    }
    private function _getCategories($data)
    {
        foreach ($data as $row) {
            $this->categories[$row['parent_id']][] = $row;
        }
    }

    private function assignLocalCategories(){
        if (defined('MY_GOODS_SYSTEM')) {
            $cms = new CMS();
            $cms->Check();

            $cms->checkTable('my_categories');
            $category = $cms->GetCategoryById();
            $this->_getCategories($category);
            $my_cats = $this->categories;
            $this->tpl->assign('my_cats', isset($my_cats) ? $my_cats : array());
        }
    }

    private function addAliasesToCategories(&$rootCats)
    {
        try {
            $SeoCatsRepository = new SeoCategoryRepository($this->cms);
            if (! is_array($rootCats)) {
                return false;
            }
            $categoriesIds = array();
            foreach ($rootCats as $item) {
                $categoriesIds[] = $item['id'];
            }
            $aliases = $SeoCatsRepository->getCategoryAliases($categoriesIds);
            $SeoCatsRepository->updateCategoryAliases($rootCats, $aliases);
            
            foreach ($rootCats as &$item) {
                if (isset($aliases[$item['id']])) {
                    $item['alias'] = $aliases[$item['id']]['alias'];
                }
            }
        } catch (DBException $e) {
            Session::setError($e->getMessage(), 'DBError');
        }
        return true;
    }

    public function convertCategoriesArrayToThreeLevelsTree($rootCats){
        $treeCats = array();
        $parents = array();

        if ($rootCats) foreach($rootCats as $c){
            $parents[$c['Id']] = $c['ParentId'];
            if(! $c['ParentId'] || $c['ParentId'] == $this->whRootParentId || $c['ParentId'] == $this->yjpRootParentId){
                $treeCats[$c['Id']] = array_merge($c, array('children' => array(), 'level' => 1));
            }
            elseif(isset($treeCats[$c['ParentId']])){
                @$treeCats[$c['ParentId']]['children'][$c['Id']] =
                    array_merge($c, array('children' => array(), 'level' => $treeCats[$c['ParentId']]['level'] + 1));
            }
            else{
                @$treeCats[$parents[$c['ParentId']]]['children'][$c['ParentId']]['children'][$c['Id']] =
                    array_merge($c, array('children' => array(), 'level' => $treeCats[$c['ParentId']]['level'] + 1));
            }
        }
        return $treeCats;
    }

    public function convertCategoriesArrayToTwoLevelsTree($rootCats){
        $treeCats = array();

        if ($rootCats) foreach($rootCats as $c){
            if(! $c['ParentId'] || $c['ParentId'] == $this->whRootParentId || $c['ParentId'] == $this->yjpRootParentId){
                $treeCats[$c['Id']] = array_merge($c, array('children' => array(), 'level' => 1));
                
            }
            elseif(isset($treeCats[$c['ParentId']])){
                @$treeCats[$c['ParentId']]['children'][$c['Id']] =
                    array_merge($c, array('children' => array(), 'level' => $treeCats[$c['ParentId']]['level'] + 1));
            }
        }

        return $treeCats;
    }
}
