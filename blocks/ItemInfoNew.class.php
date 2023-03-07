<?php

class ItemInfoNew extends GenerateBlock
{
    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = 'iteminfonew';
    protected $_template_path = '/main/';
    private $baseUrl;

    const DEFAULT_CONFIGURATION = 1;


    /**
     * @var UserData
     */
    protected $userData;

    public function __construct()
    {
        parent::__construct(true);

        $this->baseUrl = new UrlWrapper();
        $this->baseUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

        $this->userData = new UserData();
    }

    protected function setVars()
    {
        try {
            if(defined('CFG_LOAD_ITEM_VIA_AJAX') && CFG_LOAD_ITEM_VIA_AJAX && !@$_GET['getItemInfo']){
                $this->_template = 'iteminfonew_ajax';
                return ;
            }

            $id = RequestWrapper::getValueSafe('id');
            $vendorID = RequestWrapper::getValueSafe('vendorId');
            $quantity = isset($_POST['quantity'])?$_POST['quantity']:1;

            if (isset($_POST['add'])) {
                // удалить файл кеша корзины и избранного
                $this->fileMysqlMemoryCache->DelCacheEl('BatchGetUserData:'.Session::getUserOrGuestSession());

                Basket::addToBasket($this->otapilib, $id, $quantity);
                header('Location: '.$_SERVER['REQUEST_URI']);
            }

            $blockList = array('DeliveryCosts', 'Promotions', 'RootPath', 'Vendor', 'MostPopularVendorItems16');

            if (General::getConfigValue('product_tabs') == 2) {
                $blockList[] = 'Description';
            }

            $this->otapilib->setErrorsAsExceptionsOn();
            $fulliteminfo = $this->otapilib->BatchGetItemFullInfo(User::getObject()->getSid(), $id, implode(',', $blockList));
            $provider = InstanceProvider::getObject()->GetProviderInfo(Session::getActiveLang(), $fulliteminfo['Item']['ProviderType']);
            $this->otapilib->setErrorsAsExceptionsOff();

            $iteminfo = $fulliteminfo['Item'];
            if ((General::getConfigValue('hide_item_for_restrictions') && ($iteminfo['IsFiltered'] === 'true'))) {
                $this->onDenyItemShowRedirect($fulliteminfo);
            }

            $this->definePageOg($iteminfo);
            $GLOBALS['pagetitle'] = $iteminfo['title'];

            /* инфо о продавце */
            $vendorInfo = $fulliteminfo['Vendor'];
            $vendorInfo['showLinkAllGoods'] = (isset($vendorInfo['VendorItems']['EmptyList']) && $vendorInfo['VendorItems']['EmptyList']) ? false : true;

            /* другие товары продавца */
            $vendorItems = $fulliteminfo['VendorItems']['data'];
            shuffle($vendorItems); // перемешать товары
            if (count($vendorItems) > 6) {
                $vendorItems = array_slice((array)$vendorItems, 0, 6); // оставить 6 первых
            }

            // Условие для создания крошек по cid
            if ($this->request->getValue('cid') && $this->request->getValue('cid') != $iteminfo['CategoryId']) {
                $fulliteminfo['RootPath'] = $this->otapilib->GetCategoryRootPath($this->request->getValue('cid'));
                $fulliteminfo['RootPath'] = array_reverse($fulliteminfo['RootPath']);
            }

            //К сожалению, только заглушкой можно избежать появления в крошках RootCategory
            $rootPath = array();
            foreach ($fulliteminfo['RootPath'] as $path) {
                if (($path['ExternalId'] == 'wh-0') && ($path['Name'] == 'RootCategory')) {
                    continue;
                }
                $rootPath[] = $path;
            }
            $GLOBALS['itempath'] = array_reverse($rootPath);
            $cid = '';
            if (is_array($GLOBALS['itempath'])) {
                $cid = end($GLOBALS['itempath']);
                $cid = $cid['Id'];
            }

            $GLOBALS['taoBaoCategoryId'] = $iteminfo['categoryid'];
            if (count($iteminfo['pictures'])>4) $iteminfo['pictures'] = array_slice($iteminfo['pictures'], 0, 4);

            if (ProductsHelper::isYahooProduct($iteminfo)) {
                foreach ($iteminfo['pictures'] as &$pic) {
                    $pic['ProviderType'] = $iteminfo['ProviderType'];
                }
            }
            $basketNoteVendorData = $this->userData->assignBatchGetBasketNoteVendorsData();
            $basket = $basketNoteVendorData['Basket'];
            $note = $basketNoteVendorData['Note'];
            $vendors = $basketNoteVendorData['FavoriteVendors'];
            $inNote = array();
            $inCart = array();
            if (isset($basket['Elements'])) {
                foreach($basket['Elements'] as $row){
                    if ($row['ItemId'] == $iteminfo['id']) {
                        $mas = array();
                        $mas['configurationid'] = $row['ConfigurationId'];
                        $mas['id'] = $row['Id'];
                        $inCart[] = $mas;
                    }
                }
            }
            if (isset($note)) {
                foreach($note as $row)  {
                    if ($row['ItemId'] == $iteminfo['id']) {
                        $mas = array();
                        $mas['configurationid'] = $row['ConfigurationId'];
                        $mas['id'] = $row['Id'];
                        $inNote[] = $mas;
                    }
                }
            }
            if (isset($vendors['elements'])) {
                foreach ($vendors['elements'] as $vendor) {
                    if ($vendor['itemid'] == $vendorInfo['id']) {
                        $vendorInfo['favoriteItemId'] = $vendor['id'];
                    }
                }
            }


            // Проверка на авторизованность в админке
            $admin = false;
            $ssid = Session::get('sid');
            if ($ssid) {
                try {
                    global $otapilib;
                    $otapilib->setErrorsAsExceptionsOn();
                    $webui = $otapilib->GetWebUISettings($ssid);
                    if ($otapilib->error_message !== 'SessionExpired') {
                        $admin = true;
                    }
                } catch (Exception $e) {
                    Session::clear('sid');
                }
            }
            $this->tpl->assign('admin', $admin);


            $iteminfo = $this->checkHierarchicalConfigurators($iteminfo);
            $iteminfo = $this->removeDublicatesFromProperties($iteminfo);

            // добавляем картинку для товара, выбраннную админом
            $setsRepository = new SetsRepository($this->cms);
            $iteminfo = $setsRepository->addCutomImageForItem($iteminfo, Session::getActiveLang());
            //------------------

            if (! empty($iteminfo['Description']) && General::getConfigValue('hide_item_external_links_in_description')) {
                // удаляет ссылки из описания
                $iteminfo['Description'] = preg_replace('~(href=[\'|"|](http|https)?[^>]*)~is', '', $iteminfo['Description']);
                $iteminfo['Description'] = ProductsHelper::prepareDescription($iteminfo['Description']);
            } elseif (! empty($iteminfo['Description'])) {
                $iteminfo['Description'] = ProductsHelper::prepareDescription($iteminfo['Description']);
            }

            $this->tpl->assign('defaultCurrencySign', User::getObject()->getCurrencySign());
            $this->tpl->assign('defaultDeliveryMode', User::getObject()->getUserPreferences()->GetExternalDeliveryId());

            $this->isFromExtendedSearch();

            $this->tpl->assign('isWarehouseProduct', ProductsHelper::isWarehouseProduct($iteminfo));
            $this->tpl->assign('isYahooProduct', ProductsHelper::isYahooProduct($iteminfo));

            $this->tpl->assign('inCart', $inCart);
            $this->tpl->assign('inNote', $inNote);

            $this->tpl->assign('ItemNotExists', false);
            $this->tpl->assign('iteminfo', $iteminfo);
            $this->tpl->assign('quantityRanges', $this->getQuantityRanges($iteminfo));
            $promo = array();
            if (isset($iteminfo["Promotions"])) {
                if (isset($iteminfo["Promotions"]["ConfiguredItems"])) {
                    if (count($iteminfo["Promotions"]["ConfiguredItems"])==0) {
                        $promo = $iteminfo["Promotions"];
                    } else {
                        if (count($iteminfo["Promotions"]["ConfiguredItems"]) > 1) {
                            $promo = $iteminfo["Promotions"]["ConfiguredItems"];
                        } else {
                            $promo = $iteminfo["Promotions"]["ConfiguredItems"][0];
                        }
                    }
                } else {
                    $promo = $iteminfo["Promotions"];
                }
            }
            $this->tpl->assign('promo', $promo);
            $this->tpl->assign('cid', $cid);

            $this->tpl->assign('vendorInfo', $vendorInfo);
            $this->tpl->assign('vendorItems', $vendorItems);
            $this->tpl->assign('hasItemReviews', $provider->HasItemReviews());

            $discountGroup = User::getObject()->getDiscountGroupName();
            $this->tpl->assign('discountGroup', $discountGroup);
            $this->tpl->assign('showReviewTab', RequestWrapper::getParamExists('reviewId'));
        } catch (ServiceException $e) {
            $this->_template = 'iteminfoempty';

            if (OTBase::isAdmin()) {
                ErrorHandler::registerError($e);
            }

            if ($e->getErrorCode() == 'NotFound') {
                header('HTTP/1.0 404 Not Found');
                $this->tpl->assign('ItemNotFound', true);
            } else {
                $this->tpl->assign('ItemNotFound', false);
            }

            $this->tpl->assign('NotAvailable', 'NotAvailable' === $e->getErrorCode() ? 1 : 0);

            return ;
        } catch (Exception $e) {
            $this->_template = 'iteminfoempty';
            $this->tpl->assign('ItemNotFound', false);
            if (OTBase::isAdmin()) {
                ErrorHandler::registerError($e);
            }
            return ;
        }
    }

    private function definePageOg($iteminfo)
    {
        if (!empty($iteminfo['title'])){
            General::$_page['og']['title'] = $iteminfo['title'];
        }
        if (!empty($iteminfo['mainpictureurl'])){
            General::$_page['og']['image'] =  $iteminfo['mainpictureurl'];
        }
    }

    public function checkHierarchicalConfigurators ($iteminfo) {
        if (! isset($iteminfo['HasHierarchicalConfigurators'])) {
            return $iteminfo;
        }
        if ($iteminfo['HasHierarchicalConfigurators'] == 'false') {
            return $iteminfo;
        }
        $existingConfigs = array();
        $newConfigs = array(
            "id"    =>  self::DEFAULT_CONFIGURATION,
            'name'  =>  Lang::get('configuration'),
            'values'=>  array()    
        );
        $i = 0;
        /* Перебор всех сущеcтвующих конфигураций товара */
        foreach ($iteminfo['item_with_config'] as $id => &$item) {
            $existingConfigs[$id] = $item['config'];
            $configValues = array();
            $newConfigs['values'][$i] = array(
                'id'    => $i+1,
                'name'  =>  array(),
                'alias'  =>  array(),
                'name_cny'  => array(),
                'imageurl'  => '',
                'miniimageurl'  => ''
            );

            foreach ($item['config'] as $configId => $configValue) {
                                    
                foreach ($iteminfo['configurations'][$configId]['values'] as $value) {
                    
                    if ($value['id'] == $configValue) {
                        $newConfigs['values'][$i]['name'][] =  $value['name'];
                        $newConfigs['values'][$i]['name_cny'][] =  $value['name_cny'];
                        
                        if (strlen(trim($value['alias']))) {
                            $newConfigs['values'][$i]['alias'][] =   $value['alias'];
                        } else {
                            $newConfigs['values'][$i]['alias'][] =   $value['name'];
                        }

                        if (strlen(trim($value['alias_cny']))) {
                            $newConfigs['values'][$i]['alias_cny'][] =   $value['alias_cny'];
                        } else {
                            $newConfigs['values'][$i]['alias_cny'][] =   $value['name'];
                        }

                        if (strlen($value['imageurl'])) {
                            $newConfigs['values'][$i]['imageurl'] =  $value['imageurl'];
                        }
                        if (strlen($value['miniimageurl'])) {
                            $newConfigs['values'][$i]['miniimageurl'] =  $value['miniimageurl'];
                        }
                    }
                }                  
            }
            $newConfigs['values'][$i]['alias'] = implode(', ', $newConfigs['values'][$i]['alias']);
            $newConfigs['values'][$i]['alias_cny'] = implode(', ', $newConfigs['values'][$i]['alias_cny']);

            $newConfigs['values'][$i]['name'] = implode(', ', $newConfigs['values'][$i]['name']);
            $newConfigs['values'][$i]['name_cny'] = implode(', ', $newConfigs['values'][$i]['name_cny']);

            $item['config'] = array ();
            $item['config'][self::DEFAULT_CONFIGURATION] = $i+1;
            $i++;
        }

        $iteminfo['configurations'] = array(self::DEFAULT_CONFIGURATION => $newConfigs);

        return $iteminfo;
    }
    
    
    public function removeDublicatesFromProperties ($iteminfo) {
        if ( (! isset($iteminfo['properties'])) || (! is_array($iteminfo['properties'])) ) {
            return $iteminfo;
        }
        $newProperties = array();
        foreach ($iteminfo['properties'] as $propertyArr) {
            foreach ($propertyArr as $property) { 
                if (($property['id'] != '21541') || (! General::getConfigValue('hide_item_property_price_range'))) { 
                    $newProperties[md5($property['name'])]['name'] = $property['name'];
                    if (empty($newProperties[md5($property['name'])]['value'])) {
                        $newProperties[md5($property['name'])]['value'] = $property['value'] . ';';
                    } else {
                        $newProperties[md5($property['name'])]['value'] .= $property['value'] . ';';                
                    }
                }
            }
        }
        foreach ($newProperties as &$property) {
            $parts = explode(";", $property['value']);
            $parts = array_unique($parts);
            $property['value'] = implode("; ", $parts);
        }
        $iteminfo['propertiesEdited'] = $newProperties;
        return $iteminfo;
    }
    

    private function onDenyItemShowRedirect($fulliteminfo)
    {
        $urlRedirect = '';
        $iteminfo = $fulliteminfo['Item'];

        if (!empty($iteminfo['CategoryId']) && !in_array('ByCategoryId', $iteminfo['FilterReasons'])) {
            $urlRedirect = General::generateUrl('category', array(
                'Id' => $iteminfo['CategoryId'],
                'Name' => $fulliteminfo['RootPath'][0]['Name']
            ));
        } elseif (!empty($iteminfo['VendorId']) && !in_array('ByVendorId', $iteminfo['FilterReasons'])) {
            $urlRedirect = General::generateUrl('vendor', $iteminfo['VendorId']);
        } else {
            $this->baseUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]");
            $urlRedirect = $this->baseUrl->Get();
        }
        $this->request->LocationRedirect($urlRedirect);
    }
    
    /**
     * @param RequestWrapper $request
     */
    public function GetTotalPriceAction($request){
        $count = $request->getValue('count');
        $id = $request->getValue('id');
        $promoId = $request->getValue('promoid', 0);
        $confId = $request->getValue('confid', 0);

        try {
            $this->otapilib->setErrorsAsExceptionsOn();
            $rawData = $this->otapilib->BatchGetItemTotalCost(Session::getUserOrGuestSession(), $count, $id, $promoId, $confId, 'DeliveryModes,AdditionalPrices');
            $this->otapilib->setErrorsAsExceptionsOff();
        } catch (Exception $e) {
            $this->throwAjaxError($e);
        }

        $this->sendAjaxResponse($rawData);
    }

    private function isFromExtendedSearch()
    {
        $url = new UrlWrapper();
        $url->Set($this->request->env('HTTP_REFERER', ''));
        $this->tpl->assign('isFromExtendedSearch', $url->GetKey('SearchMethod') == 'Extended' && $url->GetKey('Provider') == 'Taobao');
    }

    public function itemComments()
    {
        $this->_template = 'itemcomments';
        $reviewId = $this->request->getValue('reviewId');
        $itemId = $this->request->getValue('itemid');
        $language = Session::getActiveLang();
        $sid = User::getObject()->getSid();
        $source = "Internal";

        $reviewsProvider = new ReviewsProvider();
        $errorCode = '';
        $reviews = null;

        if (is_numeric($reviewId)) { // единичный отзыв
            try {
                $response = $reviewsProvider->getItemReview($reviewId, $language, $sid);
                $reviews = $response->GetResult();

                $reviews = View::fetchTemplate('itemcomments', 'itemcomments', '/', array(
                    'reviews' => $reviews,
                    'showAllReviewsButton' => true
                ));

            } catch (ServiceException $e) {
                $errorCode = $e->getErrorCode();
            }
        }
        // если $reviewId не числовой или не удалось получить
        // отзыв по $reviewId, получить список отзывов
        if (!isset($reviews)) {
            $framePosition = $this->request->getValue('from', 0);
            $frameSize = 20;

            try {
                $response = $reviewsProvider->searchItemReviews($language, $sid, $itemId, $source, $frameSize, $framePosition);
                $reviews = $response->GetContent();

                $reviews = View::fetchTemplate('itemcomments', 'itemcomments', '/', array(
                    'itemId' => $itemId,
                    'reviews' => $reviews,
                    'from' => $framePosition,
                    'perpage' => $frameSize,
                    'totalCount' => $response->GetTotalCount(),
                    'showAllReviewsButton' => false
                ));

            } catch (Exception $e) {
                Session::setError($e->getMessage());
            }
        }
        $this->sendAjaxResponse(array(
            'errorCode' => $errorCode,
            'reviews' => $reviews)
        );
    }

    private function getQuantityRanges($iteminfo)
    {
        $quantityRanges = array();

        if (! empty($iteminfo['QuantityRanges'])) {
            $priceCode = User::getObject()->getCurrencyCode();

            foreach ($iteminfo['QuantityRanges'] as $rangeKey => $range) {
                $quantityRange = array();

                if (count($iteminfo['QuantityRanges']) > 5 && 1 < $rangeKey && $rangeKey < count($iteminfo['QuantityRanges']) - 2) {
                    if ($rangeKey == 2) {
                        $quantityRange['DisplayRange'] = '...';
                        $quantityRange['Price'] = '...';
                        $quantityRanges[] = $quantityRange;
                    }
                    continue;
                }

                $minQuantity = $range['MinQuantity'];
                $quantityRange['MinRange'] = $minQuantity;

                if (isset($iteminfo['QuantityRanges'][$rangeKey + 1]['MinQuantity']) && !empty($iteminfo['QuantityRanges'][$rangeKey + 1]['MinQuantity'])) {
                    $maxQuantity = $iteminfo['QuantityRanges'][$rangeKey + 1]['MinQuantity'] - 1;
                } else {
                    $maxQuantity = null;
                }

                if ($maxQuantity !== null) {
                    if ($minQuantity != $maxQuantity) {
                        $quantityRange['DisplayRange'] = $minQuantity . ' - ' . $maxQuantity;
                    } else {
                        $quantityRange['DisplayRange'] = $minQuantity;
                    }
                } else {
                    $quantityRange['DisplayRange'] = '&ge; ' . $minQuantity;
                }
                $quantityRange['DisplayRange'] .= ' ' . Lang::get('pcs');

                foreach ($range['Price']['PriceWithoutDelivery'] as $price) {
                    if ($price['Code'] == $priceCode) {
                        $quantityRange['Price'] = General::getHtmlPrice($price);
                        $quantityRange['PriceSign'] = $price['Sign'];
                        break;
                    }
                }
                $quantityRanges[] = $quantityRange;
            }
        }
        return $quantityRanges;
    }
}
