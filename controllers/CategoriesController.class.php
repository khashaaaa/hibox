<?php

class CategoriesController extends GeneralContoller
{
    protected $whRootParentId = 'wh-0';
    protected $yjpRootParentId = 'yjp-0';

    public function __construct()
    {                
        parent::__construct(true);
    }

    public function defaultAction()
    {
        return "";
    }

    public function renderMenuAction()
    {
        $lang = Session::getActiveLang();
        $cacheKey = "Menu:categories_" . $lang;

        if ($this->fileMysqlMemoryCache->Exists($cacheKey)) {
            $categoriesMenu = $this->fileMysqlMemoryCache->GetCacheEl($cacheKey);
        } else {
            $categoriesMenu = $this->renderPartial('controllers/categories/preloader');
        }

        return $categoriesMenu;
    }

    public function generateMenuAction()
    {
        $lang = Session::getActiveLang();
        $cacheKey = "Menu:categories_" . $lang;

        try {
            if ($this->fileMysqlMemoryCache->Exists($cacheKey)) {
                $categoriesMenu = $this->fileMysqlMemoryCache->GetCacheEl($cacheKey);
            } else {
                $categoriesMenu = $this->getCategoriesMenu();
                $this->fileMysqlMemoryCache->AddCacheEl($cacheKey, 86400, $categoriesMenu);
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse(array(
            'html' => $categoriesMenu
        ));
    }

    public function getMenuAction()
    {
        $language = Session::getActiveLang();
        $updater = CategoriesMenuUpdater::getInstance();
        $treeCats = $updater->getData($language);

        return $this->renderPartial('controllers/categories/menu', [
            'categories' => $treeCats
        ]);
    }

    public function getCategoriesAction()
    {
        return $this->getCategoriesMenu();
    }

    private function getCategoriesMenu()
    {
        $menuType = General::getConfigValue('menu_type', 1);

        if ($menuType == '3') {
            $rootCats = $this->otapilib->GetThreeLevelRootCategoryInfoList();
        } else {
            $rootCats = $this->otapilib->GetTwoLevelRootCategoryInfoList();
        }

        if (CMS::IsFeatureEnabled('Seo2')) {
            $this->addAliasesToCategories($rootCats);
        }

        if ($menuType == '3') {
            $treeCats = $this->convertCategoriesArrayToThreeLevelsTree($rootCats);
        } else {
            $treeCats = $this->convertCategoriesArrayToTwoLevelsTree($rootCats);                
        }

        return $this->renderPartial('controllers/categories/menu' . $menuType, [
            'categories' => $treeCats,
            'level1MaxCount' => General::getConfigValue('menu_count_lvl1', 10),
            'level2MaxCount' => General::getConfigValue('menu_count_lvl2', 4)
        ]);
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
                    $item['alias'] = rawurlencode($aliases[$item['id']]['alias']);
                }
            }
        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }

        return true;
    }

    public function convertCategoriesArrayToThreeLevelsTree($rootCats) 
    {
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
    
    public function convertCategoriesArrayToTwoLevelsTree($rootCats) 
    {
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

    public function allCategoriesAction() 
    {
        try {
            $language = Session::getActiveLang();
            Otapilib2::GetTwoLevelRootCategoryInfoList($language, $rootCats);
            Otapilib2::makeRequests();

            $rootCats = $rootCats->GetCategoryInfoList()->GetContent()->GetItem()->toArray();
            UrlGenerator::addCategoriesForWarmup($rootCats);
            UrlGenerator::warmupCategoryAlias();

            $rootCats = $this->convertCategoriesToArray($rootCats);
            $categories = $this->convertCategoriesArrayToTwoLevelsTree($rootCats);
        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }

        return $this->render('controllers/categories/all-categories', [
            'categories' => $categories
        ]);
    }

    public function getVisibleCategoriesArray($rootCats) 
    {
        $visibleRootCats = array();
    
        if ($rootCats) foreach($rootCats as $c) {
            if ($c['IsHidden'] == 'true') {
                continue; 
            }

            $visibleRootCats[] = $c;
        }
    
        return $visibleRootCats;
    }

    public function getSubcategoriesAction()
    {
        $categories = array();
        try {
            $cid = $this->request->getValue('cid');
            $categories = $this->otapilib->GetCategorySubcategoryInfoList($cid);
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $output = $this->renderPartial('controllers/categories/get-subcategories', [
            'categories' => $this->getVisibleCategoriesArray($categories)
        ]);

        $this->sendAjaxResponse(array(
            'html' => $output
        ));
    }

    public function convertCategoriesToArray($rootCats)
    {
        $categories = [];

        if (! empty($rootCats)) {
            foreach ($rootCats as $cat) {
                if ($cat->IsHidden() == 'true') {
                    continue;
                }
                $category = [];
                $category['Id'] = $cat->GetId();
                $category['Name'] = $cat->GetName();
                $category['ParentId'] = $cat->GetParentId();
                $category['IsParent'] = $cat->IsParent();
                $category['Url'] = UrlGenerator::generateSearchUrlByParams([
                    'cid' => $cat->GetId(),
                    'OtapiCategory' => $cat
                ]);
                foreach ($cat->GetMetaData()->GetItem() as $dataItem) {
                    $category[$dataItem->GetNameAttribute()] = $dataItem->asString();
                }
                $categories[] = $category;
            }
        }

        return $categories;
    }
}
