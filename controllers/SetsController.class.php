<?php

class SetsController extends GeneralContoller
{
    /**
     * @var SetsRepository
     */
    private $setsRepository;

    /**
     * @var PristroyRepository
     */
    private $pristroyRepository;

    public function __construct()
    {                
        parent::__construct(true);
        $this->otapilib->setErrorsAsExceptionsOn();

        $this->pristroyRepository = new PristroyRepository($this->cms);
        $this->setsRepository = new SetsRepository($this->cms);
    }

    public function defaultAction()
    {
        return '';
    }

    public function renderCategoriesSetsAction()
    {
        $result = '';
        $parametersArray = SetsUpdater::getCategoryItemsParams();
        $ratingsList = $this->setsRepository->getRatingsList($parametersArray, true);
        $categories = array();
        foreach ($parametersArray as $parameters) {
            $ratingList = $ratingsList[$parameters['contentType']][$parameters['type']][$parameters['catId']];
            $categories[$parameters['catId']]['name'] = $ratingList['displayName'];
            $categories[$parameters['catId']]['items'] = $ratingList['items'];
        }

        if (! empty($categories) && count($categories) > 0) {
            foreach ($categories as $catId => $category) {
                if (empty($category['items'])) {
                    continue;
                }
                $result .= $this->renderPartial('controllers/sets/items', [
                        'list' => $category['items'],
                        'type' => 'category',
                        'title' => $category['name'],
                        'categoryId' => $catId,
                        'seeAllLink' => UrlGenerator::generateCategoryUrl(array('Id' => $catId, 'Name' => $category['name']), true),
                        'noIndex' => General::getConfigValue('no_index_sets'),
                    ]);
            }
        }
        
        return $result;  
    }
    
    public function renderBestCategoriesSetsAction()
    {
        $result = '';
        $parameters = SetsUpdater::getBestCategoriesParams();

        if ($parameters['count'] > 0) {
            $ratingsList = $this->setsRepository->getRatingsList(array($parameters), true);
            $list = $ratingsList[$parameters['contentType']][$parameters['type']][$parameters['catId']]['items'];

            if (isset($list) && ! empty($list) && count($list) > 0) {
                $result .= $this->renderPartial('controllers/sets/categories', [
                    'list' => $list,
                    'noIndex' => General::getConfigValue('no_index_sets'),
                ]);
            }
        }
        return $result;
    }

    public function renderRecommendSetsAction()
    {
        $result = '';
        $parameters = SetsUpdater::getBestItemsParams();

        if ($parameters['count'] > 0) {
            $ratingsList = $this->setsRepository->getRatingsList(array($parameters), true);
            $list = $ratingsList[$parameters['contentType']][$parameters['type']][$parameters['catId']]['items'];

            if (isset($list) && ! empty($list) && count($list) > 0) {
                $result .= $this->renderPartial('controllers/sets/items', [
                        'list' => $list,
                        'type' => 'recom_goods',
                        'title' => false,
                        'seeAllLink' => false,
                        'noIndex' => General::getConfigValue('no_index_sets'),
                    ]);
            }
        }
        return $result;
    }
    
    public function renderPopularSetsAction()
    {
        $result = '';
        $parameters = SetsUpdater::getPopularItemsParams();

        if ($parameters['count'] > 0) {
            $ratingsList = $this->setsRepository->getRatingsList(array($parameters),true);
            $list = $ratingsList[$parameters['contentType']][$parameters['type']][$parameters['catId']]['items'];

            if (isset($list) && !empty($list) && count($list) > 0) {
                $result .= $this->renderPartial('controllers/sets/items', [
                        'list' => $list,
                        'type' => 'popular_goods',
                        'title' => false,
                        'seeAllLink' => false,
                        'noIndex' => true,
                    ]);
            }
        }
        
        return $result;
    }
    
    public function renderPristroySetsAction()
    {
        $result = '';
        
        if (CMS::IsFeatureEnabled('FleaMarket')) {
            $itemsCount = General::getConfigValue('items_with_pristroy', 8);
            $items = $itemsCount ? $this->pristroyRepository->getList(0, $itemsCount) : array();
            
            if (! empty($items['data'])) {
                $result .= $this->renderPartial('controllers/sets/pristroy-items', [
                        'list' => $items['data'],
                        'type' => 'Pristroy',
                        'seeAllLink' =>  UrlGenerator::generatePristroyUrl(),
                        'title' => false
                    ]);
            }
        }
        
        return $result;
    }
    
    public function renderLastViewedSetsAction()
    {
        $result = '';
        $parameters = SetsUpdater::getLastItemsParams();

        if ($parameters['count'] > 0) {
            $ratingsList = $this->setsRepository->getRatingsList(array($parameters), true);
            $list = $ratingsList[$parameters['contentType']][$parameters['type']][$parameters['catId']]['items'];

            if (isset($list) && !empty($list) && count($list) > 0) {
                $result .= $this->renderPartial('controllers/sets/items', [
                        'list' => $list,
                        'type' => 'last_viewed_goods',
                        'title' => false,
                        'seeAllLink' => false,
                        'noIndex' => true,
                    ]);
            }
        }
        
        return $result;
    }
    
    public function renderItemsWithReviewSetsAction($parameters = array())
    {
        $result = '';
        $list = array();
        $title = false;

        try {
            $language = Session::getActiveLang();
            $setsUpdater = SetsUpdater::getInstance();
            $reviewedItems = $setsUpdater->getReviewedItems($language);
            if (!empty($reviewedItems)) {
                $list = $reviewedItems['items'];
                $title = $reviewedItems['displayName'];
            }

        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }
                
        if (! empty($list)) {
            $result .= $this->renderPartial('controllers/sets/itemsReviews', [
                    'list' => $list,
                    'type' => 'with_reviews',
                    'cssWrapper' => (isset($parameters['wide']) && $parameters['wide']) ? 'products-with-reviews' : '',
                    'title' => $title,
                    'seeAllLink' => UrlGenerator::generateReviewsUrl(),
                    'noIndex' => true,
                ]);
        }
        
        return $result;
    }
    
    public function renderWarehouseSetsAction()
    {
        $result = '';
        $parameters = SetsUpdater::getWarehouseItemsParams();

        if ($parameters['count'] > 0) {
            $ratingsList = $this->setsRepository->getRatingsList(array($parameters), true);
            $list = $ratingsList[$parameters['contentType']][$parameters['type']][$parameters['catId']]['items'];

            if (isset($list) && !empty($list) && count($list) > 0) {
                $result .= $this->renderPartial('controllers/sets/items', [
                        'list' => $list,
                        'type' => 'warehouse_items',
                        'title' => Lang::get("Warehouse_goods"),
                        'seeAllLink' => false,
                        'noIndex' => General::getConfigValue('no_index_sets'),
                    ]);
            }
        }
        
        return $result;    
    }

    public function renderBrandsSetsAction()
    {
        $result = '';
        $parameters = SetsUpdater::getBrandsItemsParams();

        if ($parameters['count'] > 0) {
            $ratingsList = $this->setsRepository->getRatingsList(array($parameters), true);
            $list = $ratingsList[$parameters['contentType']][$parameters['type']][$parameters['catId']]['items'];

            if (isset($list) && !empty($list) && count($list) > 0) {
                $result .= $this->renderPartial('controllers/sets/brands', [
                        'list' => $list,
                        'type' => 'popular_brands',
                        'title' => false,
                        'seeAllLink' => UrlGenerator::generateBrandsUrl()
                    ]);
            }
        }
        
        return $result;
    }
    
    public function renderVendorSetsAction()
    {
        $result = '';
        $parameters = SetsUpdater::getVendorsItemsParams();
        $SeoCatsRepository = new SeoCategoryRepository($this->cms);
        $vendorIds = array();
        if ($parameters['count'] > 0) {
            $ratingsList = $this->setsRepository->getRatingsList(array($parameters), true);
            $list = $ratingsList[$parameters['contentType']][$parameters['type']][$parameters['catId']]['items'];
            foreach ($list as $value) {
                $vendorIds[] = $value['id'];
            }

            $aliases = $SeoCatsRepository->getVendorAliases($vendorIds);

            if (!empty($aliases)) {
                foreach ($list as $key => $vendor) {
                    foreach ($aliases as $alias) {
                        if (($vendor['id'] === $alias['vendor_id']) && ($alias['alias'])) {
                            $list[$key]['alias'] = $alias['alias'];
                            continue;
                        }
                    }
                }
            }
            if (isset($list) && !empty($list) && count($list) > 0) {
                $result .= $this->renderPartial('controllers/sets/vendors', [
                        'list' => $list,
                        'type' => 'best_vendors',
                        'title' => false,
                        'seeAllLink' => UrlGenerator::generateVendorsUrl()
                    ]);
            }
        }
        
        return $result;
    }
}
