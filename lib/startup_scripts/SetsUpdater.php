<?php

class SetsUpdater
{
    CONST DEFAULT_SETS_TTL = 3600; // 1 час (60*60);
    CONST INCREASE_SETS_TTL = 604800; // 7 дней (60*60*24*7);

    /** @var CMS */
    private $cms;

    /** @var OTAPIlib */
    private $otapilib;

    /** @var FileAndMysqlMemoryCache */
    private $fileMysqlMemoryCache;

    /**
     * @var SetsUpdater
     */
    protected static $_instance;

    /**
     * @var array $ratingsList хранилище полученных
     * ранее подборок
     */
    private $ratingsList = array();

    private function __construct() {
        $this->cms = General::getCms();
        $this->fileMysqlMemoryCache = new FileAndMysqlMemoryCache($this->cms);
        $this->initOTAPILib();
    }

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    private function __clone() {
    }

    private function __wakeup() {
    }

    private function initOTAPILib()
    {
        global $otapilib;
        $this->otapilib = $otapilib;
        $this->otapilib->setErrorsAsExceptionsOn();
    }

    /**
     * Получить подборки в зависимости от
     * конфигурации в $setsParameters
     * @param string $language
     * @param array $setsParameters - конфигурации подборок
     * @param bool $realTime - время кэша без увеличения
     * @return array
     * @throws Exception
     */
    public function getData($language, array $setsParameters = array(), $realTime = false)
    {
        // если нет конфига с параметрами, получить конфиги всех подборок
        if (empty($setsParameters)) {
            $setsParameters = $this->getSetsConfig();
        }

        $xmlParams = new SimpleXMLElement('<BatchRatingListSearchParameters></BatchRatingListSearchParameters>');
        $ratingListsXml = $xmlParams->addChild('RatingLists');
        $countRequestedSets = 0;
        foreach ($setsParameters as $parameters) {
            if ($parameters['count'] == 0) {
                continue;
            }
            $setHash = self::getRatingListHashKey($parameters['contentType'], $parameters['type'], $parameters['catId'], Session::getActiveLang());
            if (!isset($this->ratingsList[$setHash])) {
                $cacheKey = self::getRatingListCacheKey($parameters['contentType'], $parameters['type'], $parameters['catId'], Session::getActiveLang());
                if ($this->needUpdate($cacheKey, $realTime)) {
                    $this->addRatingListNode($parameters, $ratingListsXml);
                    $countRequestedSets++;
                }
            }
        }

        if ($countRequestedSets > 0) {
            /** @var $setsData OtapiBatchRatingListsSearchResultAnswer */
            OTAPILib2::BatchSearchRatingLists($language, $xmlParams->asXML(), $setsData);
            OTAPILib2::makeRequests();
            $sets = array(
                $setsData->GetResult()->GetItems(),
                $setsData->GetResult()->GetVendors(),
                $setsData->GetResult()->GetBrands(),
                $setsData->GetResult()->GetCategories(),
                $setsData->GetResult()->GetSearchStrings()
            );
        } else {
            $sets = array();
        }

        // Сохранить полученные подборки в кэш
        foreach ($sets as $set) {
            foreach ($set->GetRatingList() as $ratingList) {
                $cacheKey = self::getRatingListCacheKey($ratingList->GetContentType(), $ratingList->GetItemRatingType(), $ratingList->GetCategoryId(), Session::getActiveLang());
                $parameters = $this->getSetConfig($ratingList->GetItemRatingType(), $ratingList->GetContentType(), $ratingList->GetCategoryId());

                $ttl = ($ratingList->HasError() || $setsData->HasTranslateErrors()) ? 60 : $parameters['cacheTime'];
                $ttl += self::INCREASE_SETS_TTL;
                $content = ($ratingList->HasError()) ? '' : $ratingList->asXml();
                $this->fileMysqlMemoryCache->AddCacheEl($cacheKey, $ttl, $content);
            }
        }

        return $this->generateResponse($language, $setsParameters);
    }

    private function generateResponse($language, array $setsParameters)
    {
        $setsRepository = new SetsRepository(General::getCms());
        // Генерация ответа в формате otapilib1
        $response = array();
        foreach ($setsParameters as $parameters) {
            if ($parameters['count'] == 0) {
                continue;
            }
            $setHash = self::getRatingListHashKey($parameters['contentType'], $parameters['type'], $parameters['catId'], Session::getActiveLang());
            if (!isset($this->ratingsList[$setHash])) {
                $cacheKey = self::getRatingListCacheKey($parameters['contentType'], $parameters['type'], $parameters['catId'], Session::getActiveLang());
                if ($this->fileMysqlMemoryCache->Exists($cacheKey)) {
                    $list = array();
                    try {
                        $listXml = $this->fileMysqlMemoryCache->GetCacheEl($cacheKey);
                        $ratingList = new $parameters['otapi2type'](simplexml_load_string($listXml));
                        $ratingListResultXml = $ratingList->GetResult()->asXML();

                        if (empty($ratingListResultXml)) {
                            throw new Exception(Lang::get('could_not_get_a_set') . ' ' . $parameters['contentType'] . ':' . $parameters['type']);
                        }
                        // перевести $ratingList в формат otapilib1
                        if (!empty($parameters['otapiMethod'])) {
                            $list = $this->otapilib->{$parameters['otapiMethod']}(array(), 0, $parameters['count'], $ratingListResultXml);
                        } else {
                            $list = $ratingListResultXml = $ratingList->GetResult();
                        }

                    } catch (Exception $e) {
                        if (OTBase::isTest()) {
                            ErrorHandler::registerError($e);
                        }
                    }
                    if (!empty($parameters['otapiMethod'])) {
                        if (isset($list['content'])) {
                            $list = $list['content'];
                        }
                        if ($parameters['contentType'] == 'Item') {
                            // добавление кастомных картинок товаров из БД
                            $list = $setsRepository->getSetsPictures($parameters['type'], $list, $language);

                        }
                    }
                    $this->ratingsList[$setHash] = array(
                        'displayName' => $ratingList->GetName(),
                        'items' => $list
                    );
                }
            }

            $response[$parameters['contentType']][$parameters['type']][$parameters['catId']] = $this->ratingsList[$setHash];
        }
        return $response;
    }

    private function needUpdate($cacheKey, $realTime)
    {
        if ($this->fileMysqlMemoryCache->Exists($cacheKey)) {
            if ($realTime) {
                $expireDate = $this->fileMysqlMemoryCache->getExpireDate($cacheKey);
                $realExpireDate = $expireDate - self::INCREASE_SETS_TTL;
                $needUpdate = ($realExpireDate - time()) < 0 ? true : false;
            } else {
                $needUpdate = false;
            }
        } else {
            $needUpdate = true;
        }
        return $needUpdate;
    }

    private function addRatingListNode($parameters, $ratingListsXml)
    {
        $ratingXml = $ratingListsXml->addChild('RatingList');
        $ratingXml->addChild('CategoryId', $parameters['catId']);
        $ratingXml->addChild('ItemRatingType', $parameters['type']);
        $ratingXml->addChild('IsRandomSearch', $parameters['isRandomSearch'] ? 'true' : 'false');
        $ratingXml->addChild('ContentType', $parameters['contentType']);
        $ratingXml->addChild('FramePosition', 0);
        $ratingXml->addChild('FrameSize', $parameters['count']);
    }

    public static function getRatingListCacheKey($ratingType, $contentType, $categoryId, $lang)
    {
        return 'RatingLists:' . implode('/', func_get_args());
    }

    private static function getRatingListHashKey($ratingType, $contentType, $categoryId, $lang)
    {
        return md5(serialize(func_get_args()));
    }

    public static function getBestItemsParams()
    {
        $params = array(
            'type'  => 'Best',
            'contentType' => 'Item',
            'count' => General::getNumConfigValue('items_with_best', 8),
            'catId' => 0,
            'otapiMethod' => 'SearchRatingListItems',
            'otapi2type' => 'RatingListSearchResultOfOtapiItemInfo'
        );
        return self::prepareSetParams('getBestItemsParams', $params);
    }

    public static function getPopularItemsParams()
    {
        $params = array(
            'type' => 'Popular',
            'contentType' => 'Item',
            'count' => General::getNumConfigValue('items_with_popular', 8),
            'catId' => 0,
            'otapiMethod' => 'SearchRatingListItems',
            'otapi2type' => 'RatingListSearchResultOfOtapiItemInfo'
        );
        return self::prepareSetParams('getPopularItemsParams', $params);
    }

    public static function getLastItemsParams()
    {
        $params = array(
            'type' => 'Last',
            'contentType' => 'Item',
            'count' => General::getNumConfigValue('items_with_last', 8),
            'catId' => 0,
            'otapiMethod' => 'SearchRatingListItems',
            'otapi2type' => 'RatingListSearchResultOfOtapiItemInfo'
        );
        return self::prepareSetParams('getLastItemsParams', $params);
    }

    public static function getWarehouseItemsParams()
    {
        $params = array(
            'type' => 'Best',
            'contentType' => 'Item',
            'count' => General::getNumConfigValue('warehouse_items', 8),
            'catId' => 'Warehouse',
            'otapiMethod' => 'SearchRatingListItems',
            'otapi2type' => 'RatingListSearchResultOfOtapiItemInfo'
        );
        return self::prepareSetParams('getWarehouseItemsParams', $params);
    }

    public static function getBrandsItemsParams()
    {
        $params = array(
            'type' => 'Best',
            'contentType' => 'Brand',
            'count' => General::getNumConfigValue('brand_with_best', 10),
            'catId' => 0,
            'otapiMethod' => 'SearchRatingListBrands',
            'otapi2type' => 'RatingListSearchResultOfOtapiBrandInfo'
        );
        return self::prepareSetParams('getBrandsItemsParams', $params);
    }

    public static function getVendorsItemsParams()
    {
        $params = array(
            'type' => 'Best',
            'contentType' => 'Vendor',
            'count' => General::getNumConfigValue('items_with_vendor', 8),
            'catId' => 0,
            'otapiMethod' => 'SearchRatingListVendors',
            'otapi2type' => 'RatingListSearchResultOfOtapiVendorInfo'
        );
        return self::prepareSetParams('getVendorsItemsParams', $params);
    }

    public static function getCategoryItemsParams()
    {
        $params = array();
        if (CMS::IsFeatureEnabled('RatingListForCategory')) {
            $setsRepository = new SetsRepository(General::getCms());

            foreach ($setsRepository->getSiteCategories() as $categoryId) {
                 $paramsTmp = array(
                    'type'  => 'Category',
                    'contentType' => 'Item',
                    'count' => General::getNumConfigValue('items_with_category', 8),
                    'catId' => $categoryId,
                    'otapiMethod' => 'SearchRatingListItems',
                    'otapi2type' => 'RatingListSearchResultOfOtapiItemInfo'
                );
                $paramsTmp = self::prepareSetParams('getCategoryItemsParams', $paramsTmp);
                $params[] = $paramsTmp;
            }
        }
        return $params;
    }

    public static function getReviewsItemsParams()
    {
        $params = array(
            'type' => 'Best',
            'contentType' => 'Item',
            'count' => General::getNumConfigValue('items_with_comments', 8),
            'catId' => 'ReviewedItems',
            'otapiMethod' => '',
            'otapi2type' => 'RatingListSearchResultOfOtapiItemInfo'
        );
        return SetsUpdater::prepareSetParams('getReviewsItemsParams', $params);
    }

    public static function getBestCategoriesParams()
    {
        $params = array(
            'type' => 'Best',
            'contentType' => 'Category',
            'count' => 20,
            'catId' => '0',
            'otapiMethod' => '',
            'otapi2type' => 'RatingListSearchResultOfOtapiCategory'
        );
        return SetsUpdater::prepareSetParams('getBestCategoriesParams', $params);
    }

    public static function prepareSetParams($methodName, $params)
    {
        $params['cacheTime'] = self::DEFAULT_SETS_TTL;
        $params['isRandomSearch'] = false;
        $params = Plugins::runSerialEvent('onMainPage'.$methodName, $params);
        return $params;
    }

    private function getSetsConfig()
    {
        return array_merge(
            self::getCategoryItemsParams(),
            array(
                self::getBestItemsParams(),
                self::getPopularItemsParams(),
                self::getLastItemsParams(),
                self::getWarehouseItemsParams(),
                self::getBrandsItemsParams(),
                self::getVendorsItemsParams(),
                self::getBestCategoriesParams()
            )
        );
    }

    private function getSetConfig($ratingType, $contentType, $categoryId)
    {
        $config = array();
        foreach ($this->getSetsConfig() as $parameters) {
            if ($parameters['type'] == $ratingType && $parameters['contentType'] == $contentType && (string)$parameters['catId'] == $categoryId) {
                $config = $parameters;
                break;
            }
        }
        return $config;
    }

    public static function clearCachePart($language, $contentType, $type = 'Best', $cid = 0)
    { // передать язык подборки
        $cache = new FileAndMysqlMemoryCache(new CMS());
        $parameters = array();
        if ($contentType == 'Item') {
            if ($type == 'Best') {
                $parameters[] = self::getBestItemsParams();
            } else if ($type == 'Last') {
                $parameters[] = self::getLastItemsParams();
            } else if ($type == 'Popular') {
                $parameters[] = self::getPopularItemsParams();
            } else {
                $parameters = array_merge($parameters, self::getCategoryItemsParams());
            }
        } else if ($contentType == 'Brand') {
            $parameters[] = self::getBrandsItemsParams();
        } else if ($contentType == 'Vendor') {
            $parameters[] = self::getVendorsItemsParams();
        } else if ($cid == 'Warehouse') {
            $parameters[] = self::getWarehouseItemsParams();
        }

        if (!empty($parameters)) {
            $self = self::getInstance();
            foreach ($parameters as $parameter) {
                $setHash = self::getRatingListHashKey($parameter['contentType'], $parameter['type'], $parameter['catId'], Session::getActiveLang());
                if (isset($self->ratingsList[$setHash])) {
                    unset($self->ratingsList[$setHash]);
                }
                $cacheKey = self::getRatingListCacheKey($parameter['contentType'], $parameter['type'], $parameter['catId'], $language);
                $cache->DelCacheEl($cacheKey);
            }
        }
    }

    public function getReviewedItems($language, $realTime = false)
    {
        $parameters = SetsUpdater::getReviewsItemsParams();
        if ($parameters['count'] == 0) {
            return array();
        }
        $setHash = self::getRatingListHashKey($parameters['contentType'], $parameters['type'], $parameters['catId'], $language);

        if (!isset($this->ratingsList[$setHash])) {
            $list = array();
            try {
                $cacheKey = self::getRatingListCacheKey($parameters['contentType'], $parameters['type'], $parameters['catId'], $language);
                if ($this->needUpdate($cacheKey, $realTime)) {
                    $sid = User::getObject()->getSid();
                    $reviewedItems = new OtapiItemInfoListFrameAnswer(null);
                    $xmlParams = '<SearchParameters><OrderBy>ReviewTime:Desc</OrderBy></SearchParameters>';
                    OTAPILib2::SearchReviewedItems($language, $sid, $xmlParams, 0, $parameters['count'], $reviewedItems);
                    OTAPILib2::makeRequests();

                    $ttl = $reviewedItems->HasTranslateErrors() ? 60 : $parameters['cacheTime'];
                    $ttl += self::INCREASE_SETS_TTL;
                    $this->fileMysqlMemoryCache->AddCacheEl($cacheKey, $ttl, $reviewedItems->asXml());
                }

                if ($this->fileMysqlMemoryCache->Exists($cacheKey)) {
                    $listXml = $this->fileMysqlMemoryCache->GetCacheEl($cacheKey);
                    $ratingList = new OtapiItemInfoListFrameAnswer(simplexml_load_string($listXml));

                    foreach ($ratingList->GetOtapiItemInfoSubList()->GetContent()->GetItem() as $value) {
                        $list[] = Product::getObject($value->GetId(), $value);
                    }
                }
            } catch (Exception $e) {
                if (OTBase::isTest()) {
                    ErrorHandler::registerError($e);
                }
            }

            $this->ratingsList[$setHash] = array(
                'displayName' => Lang::get('with_reviews'),
                'items' => $list
            );
        }
        return $this->ratingsList[$setHash];
    }
}