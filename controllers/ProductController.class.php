<?php

class ProductController extends GeneralContoller
{
    public function defaultAction()
    {
        $isAjax = $this->request->isAjax();

        $productId = $this->request->get('id');
        $product = Product::getObject($productId, false);
        $itemUrl = UrlGenerator::generateItemUrl($productId, []);
        $blocks = $this->getBlocksBySimplifiedItemFullInfo($product, new OtapiBatchSimplifiedItemFullInfo(null));

        $lang = Session::getActiveLang();
        $sid = User::getObject()->getSid();

        try {
            $blockList = 'RootPath,Vendor';

            $productData = new OtapiBatchSimplifiedItemFullInfoAnswer(null);
            $itemParameters = $this->getItemParameters();
            OTAPILib2::BatchGetSimplifiedItemFullInfo($lang, $sid, $productId, $blockList, $itemParameters, $productData);
            OTAPILib2::makeRequests();
            $productData = $productData->GetResult();

            if ((General::getConfigValue('hide_item_for_restrictions') && ($productData->GetItem()->IsFiltered() === true))) {
                return $this->onDenyItemShowRedirect($productData);
            }

            $product = Product::getObject($productId, $productData);

            if (!$isAjax && 'IsDeleted' === $product->notAvailableId) {
                header('HTTP/1.0 404 Not Found');
            }

            $this->definePageOg($product);
            General::$_page['title'] = $product->title;
        } catch (ServiceException $e) {
            if (OTBase::isAdmin()) {
                $this->errorHandler->registerError($e);
            }

            if ($e->getErrorCode() == 'NotFound') {
                if ($isAjax && $this->request->get('reload')) {
                    return RequestWrapper::LocationRedirect($itemUrl);
                }
                if ($isAjax) {
                    return $this->sendAjaxResponse([
                        'answer' => $this->renderPartial('controllers/product/not-found', ['productId' => $productId]),
                    ]);
                }
                header('HTTP/1.0 404 Not Found');
                return $this->render('controllers/product/not-found', ['productId' => $productId]);
            }

            if ($isAjax && $this->request->get('reload')) {
                return $this->respondAjaxError($e->getMessage());
            }
            if ($isAjax) {
                return $this->sendAjaxResponse([
                    'answer' => $this->renderPartial('controllers/product/error', [
                        'product' => $product,
                        'message' => $e->getMessage(),
                        'autoReload' => 'NotAvailable' === $e->getErrorCode() ? 1 : 0,
                    ]),
                ]);
            }

            return $this->render('controllers/product/error', [
                'product' => $product,
                'message' => $e->getMessage(),
                'autoReload' => 'NotAvailable' === $e->getErrorCode() ? 1 : 0,
            ]);
        } catch (Exception $e) {
            if (OTBase::isAdmin()) {
                $this->errorHandler->registerError($e);
            }

            if ($isAjax) {
                return $this->respondAjaxError($e->getMessage());
            }

            return $this->render('controllers/product/error', [
                'product' => $product,
                'message' => $e->getMessage(),
                'autoReload' => 0,
            ]);
        }

        $crumbs = [];
        try {
            $blocks = $this->getBlocksBySimplifiedItemFullInfo($product, $productData);

            if ($this->request->getValue('cid') && $this->request->getValue('cid') != $productData->GetItem()->GetCategory()) {
                $rootPath = new OtapiCategoryListAnswer(null);
                OTAPILib2::GetCategoryRootPath($lang, $this->request->getValue('cid'), $rootPath);
                OTAPILib2::makeRequests();
                $crumbs = CrumbsController::generateCrumbsByRootPath($rootPath->GetCategoryInfoList()->GetContent(), true);
            } else {
                $crumbs = CrumbsController::generateCrumbsByRootPath($productData->GetRootPath()->GetContent(), true);  
            }
            CrumbsController::setCrumbs($crumbs);
        } catch (Exception $e) {
            if ($isAjax) {
                $this->respondAjaxError($e->getMessage());
            }

            $this->errorHandler->registerError($e);
        }

        $viewData = [
            'product' => $product,
            'blocks' => $blocks,
            'isAjax' => $isAjax,
            'itemUrl' => $itemUrl
        ];

        if ($isAjax) {
            $this->sendAjaxResponse([
                'answer' => $this->renderPartial('controllers/product/index', $viewData),
                'crumbs' => $this->renderPartial('controllers/crumbs/crumbs', ['crumbs' => $crumbs]),
            ]);
        }

        return $this->render('controllers/product/index', $viewData);
    }

    private function getItemParameters()
    {
        $xmlParams = new SimpleXMLElement('<Parameters></Parameters>');

        if (!$this->request->get('reload')) {
            if (General::getConfigValue('product_allow_in_complete', true)) {
                $xmlParams->addAttribute('AllowIncomplete', 'true');
            }
            if (General::getConfigValue('product_allow_deleted', true)) {
                $xmlParams->addAttribute('AllowDeleted', 'true');
            }
        }

        return $xmlParams->asXML();
    }

    private function onDenyItemShowRedirect($fulliteminfo)
    {
        $iteminfo = $fulliteminfo->GetItem();
        $filterReason = $iteminfo->GetFilterReasons()->GetReason()->toArray();

        if (($iteminfo->GetCategory()) && !in_array('ByCategoryId', $filterReason)) {
            $urlRedirect = UrlGenerator::generateSearchUrlByParams(array(
                'cid' => $iteminfo->GetCategory()->asString()
            ));
        } elseif (($iteminfo->GetVendor()) && !in_array('ByVendorId', $filterReason)) {
            $urlRedirect = UrlGenerator::generateSearchUrlByParams(array(
                'vid' => $iteminfo->GetVendor()->asString()
            ));
        } else {
            $urlRedirect = UrlGenerator::getHomeUrl();
        }
        return $this->request->LocationRedirect($urlRedirect);
    }

    private function getBlocksBySimplifiedItemFullInfo(Product $product, OtapiBatchSimplifiedItemFullInfo $result)
    {
        $response = [];

        $response['Description']['needRequest'] = (bool)($result->GetDescription() === false);
        $response['Description']['html'] = $result->GetDescription();

        // Description
        if (! empty($response['Description']['html']) && General::getConfigValue('hide_item_external_links_in_description')) {
            // удаляет ссылки из описания
            $response['Description']['html'] = preg_replace('~(href=[\'|"|](http|https)?[^>]*)~is', '', $response['Description']['html']);
            $response['Description']['html'] = ProductsHelper::prepareDescription($response['Description']['html']);
        } elseif (! empty($iteminfo['Description'])) {
            $response['Description']['html'] = ProductsHelper::prepareDescription($response['Description']['html']);
        }

        // Vendor
        $response['Vendor']['needRequest'] = true;
        $response['Vendor']['html'] = '';
        if ($result->GetVendor()->GetRawData() !== false) {
            OTAPILib2::GetVendorInfo(Session::getActiveLang(), $result->GetVendor()->GetId(), $vendorInfo);
            OTAPILib2::makeRequests();
            $vendorInfo = $vendorInfo->GetVendorInfo();
            
            if ($vendorInfo->GetDisplayName()) {
                $result->GetVendor()->GetRawData()->DisplayName = $vendorInfo->GetDisplayName(); 
            }

            $vendor = $result->GetVendor();
        } else {
            $vendor = false;
        }
        if ($vendor) {
            $response['Vendor']['needRequest'] = false;
            $response['Vendor']['html'] = $this->renderPartial('controllers/product/vendor', ['vendor' => $vendor]);
        }

        // MostPopularVendorItems16
        $response['MostPopularVendorItems16']['needRequest'] = true;
        $response['MostPopularVendorItems16']['html'] = '';
        $vendorItems = $result->GetVendorItems()->GetRawData() !== false ? $result->GetVendorItems() : false;
        if ($vendorItems) {
            $response['MostPopularVendorItems16']['needRequest'] = false;

            $products = [];
            foreach ($vendorItems->GetContent()->GetItem() as $item) {
                $product = Product::getObject($item->GetId(), $item);
                if ($result->GetCurrency()) {
                    $product->setCurrencyName($result->GetCurrency()->GetDisplayNameAttribute());
                    $product->setCurrencyCode($result->GetCurrency()->asString());
                }
                $products[] = $product;
            }
            $showLinkAllGoods = ($result->GetVendorItems()->GetTotalCount() !== false);
            $response['MostPopularVendorItems16']['html'] = $this->renderPartial('controllers/product/vendor-items', [
                'products' => $products,
                'vendorId' => $product->vendorId,
                'showLinkAllGoods' => $showLinkAllGoods,
            ]);
        }

        return $response;
    }

    public function getFullInfoAction()
    {
        $productId = $this->request->get('id');
        $blocks = $this->request->request('blocks');

        $response = [];
        try {
            $lang = Session::getActiveLang();
            $sid = User::getObject()->getSid();
            $blockList = implode(',', $blocks);

            $productData = new OtapiBatchSimplifiedItemFullInfoAnswer(null);
            $itemParameters = $this->getItemParameters();
            OTAPILib2::BatchGetSimplifiedItemFullInfo($lang, $sid, $productId, $blockList, $itemParameters, $productData);
            OTAPILib2::makeRequests();
            $productData = $productData->GetResult();

            $product = Product::getObject($productId, $productData);
            $response['blocks'] = $this->getBlocksBySimplifiedItemFullInfo($product, $productData);
        } catch (Exception $e) {
            return $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse($response);
    }

    public function reviewsAction()
    {
        $reviewId = $this->request->getValue('reviewId');
        $itemId = $this->request->getValue('itemId');
        $source = $this->request->getValue('source');
        $provider = $this->request->getValue('provider');
        $language = Session::getActiveLang();
        $sid = User::getObject()->getSid();

        $reviewsProvider = new ReviewsProvider();
        $errorCode = '';
        $reviews = null;

        if ($reviewId) { // единичный отзыв
            try {
                $response = $reviewsProvider->getItemReview($reviewId, $language, $sid);
                $reviews = $response->GetResult();

                $reviews = $this->renderPartial('controllers/product/reviews', [
                    'reviews' => $reviews,
                    'showAllReviewsButton' => true
                ]);
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
                if (!empty($source)) {
                    $response = $reviewsProvider->searchItemReviews($language, $sid, $itemId, $source, $frameSize, $framePosition);
                } else {
                    $source = "Internal";
                    $response = $reviewsProvider->searchItemReviews($language, $sid, $itemId, $source, $frameSize, $framePosition);
                    $providerName = InstanceProvider::getObject()->GetProviderNameByAlias($language, $provider);
                    $providerInfo = InstanceProvider::getObject()->GetProviderInfo($language, $providerName);

                    if ($response->GetTotalCount() === "0" && $providerInfo->HasItemReviews()) {
                        $source = "Provider";
                        $response = $reviewsProvider->searchItemReviews($language, $sid, $itemId, $source, $frameSize, $framePosition);
                    }
                }

                $reviews = $response->GetContent();

                $reviews = $this->renderPartial('controllers/product/reviews', [
                    'itemId' => $itemId,
                    'reviews' => $reviews,
                    'from' => $framePosition,
                    'perpage' => $frameSize,
                    'totalCount' => $response->GetTotalCount(),
                    'showAllReviewsButton' => false,
                    'source' => $source
                ]);
            } catch (Exception $e) {
                Session::setError($e->getMessage());
            }
        }

        $this->sendAjaxResponse(array(
                'errorCode' => $errorCode,
                'reviews' => $reviews,
                'source' => $source)
        );
    }

    public function addAction() {
        $sid = Session::getUserOrGuestSession();
        $language = Session::getActiveLang();

        $itemId = $this->request->getValue('itemId');
        $itemUrl = $this->request->getValue('itemUrl');
        $externalDeliveryId = $this->request->getValue('externalDeliveryId');
        $selected = $this->request->getValue('selected');
        $xmlParams = new SimpleXMLElement('<Request></Request>');
        $el = $xmlParams->addChild('Element');
        if ($externalDeliveryId) {
            $el->addAttribute('ExternalDeliveryId', $externalDeliveryId);
        }
        $el->addAttribute('ItemId', $itemId);
        $el->addAttribute('ItemURL', $itemUrl);
        foreach ($selected as $key => $value) {
            $elSelected = $el->addChild('Selected');
            $elSelected->addAttribute('Quantity', $value);
            $elSelected->addAttribute('ConfigurationId', $key);
        }
        $fields = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
        try{
            OTAPILib2::BatchSimplifiedAddItemsToBasket($language, $sid, $fields, $res);
            OTAPILib2::makeRequests();
            User::getObject()->clearUserDataCache();
            $itemsInCart = $this->getUser()->getCountInBasket();
            return $itemsInCart;
        } catch (Exception $e) {
                return $this->respondAjaxError($e);
        }
    }

    public function addToFavouritesAction() {
        $language = Session::getActiveLang();
        $sid = Session::getUserOrGuestSession();
        $itemId = $this->request->getValue('itemId');
        $externalDeliveryId = $this->request->getValue('externalDeliveryId');
        $selected = $this->request->getValue('selected');
        $xmlParams = new SimpleXMLElement('<Fields></Fields>');
        if ($externalDeliveryId) {
            $el = $xmlParams->addChild('FieldInfo');
            $el->addAttribute('Name', 'ExternalDeliveryId');
            $el->addAttribute('Value', $externalDeliveryId);
        }
        $fields = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());

        try{
            foreach ($selected as $key => $value) {
                OTAPILib2::AddItemToNote($language, $sid, $itemId, $key, $value, $fields, $res);
                OTAPILib2::makeRequests();
            }
            User::getObject()->clearUserDataCache();
            $itemsInFav = $this->getUser()->getCountInNote();
            return $itemsInFav;
        } catch (Exception $e) {
            return $this->respondAjaxError($e);
        }
    }

    public function addVendorToFavouritesAction() {
        $favouriteVendors = new FavouriteVendors();
        try{
            $favouriteVendors->addAction($this->request);
        } catch (Exception $e) {
            return $this->respondAjaxError($e);
        }
    }

    public function getConfigurationInfoAction()
    {
        $itemId = $this->request->get('id');

        $response = [];
        try {
            $lang = Session::getActiveLang();
            $sid = User::getObject()->getSid();
            $blockList = 'DeliveryModes,AdditionalPrices';
            $xmlParams = $this->generateConfigurationInfoXML($this->request);

            $request = new OtapiBatchSimplifiedItemConfigurationInfoAnswer(null);
            OTAPILib2::BatchGetSimplifiedItemConfigurationInfo($lang, $sid, $itemId, $xmlParams, $blockList, $request);
            OTAPILib2::makeRequests();

            if ($request->GetResult()->GetRawData()) {
                $response = XMLHelper::xmlToArray($request->GetResult()->GetRawData(), [
                    'textContent' => 'Value',
                    'attributePrefix' => '',
                    'alwaysArray' => ['AdditionalPrices', 'Value', 'Mode', 'Property', 'Range'],
                ]);
            }
        } catch (Exception $e) {
            return $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse($response);
    }

    private function generateConfigurationInfoXML($request)
    {
        $current = $request->request('current');
        $selected = $request->request('selected');

        $xmlParams = new SimpleXMLElement('<Request></Request>');

        if (!empty($current)) {
            $nodeCurrent = $xmlParams->addChild('Current');
        }
        if (!empty($current['configurationId']) && empty($current['property'])) {
            $nodeCurrent->addAttribute('ConfigurationId', $current['configurationId']);
        }
        if (!empty($current['quantity'])) {
            $nodeCurrent->addAttribute('Quantity', $current['quantity']);
        }
        if (!empty($current['property'])) {
            foreach ($current['property'] as $propertyId => $propertyValue) {
                $nodeProperty = $nodeCurrent->addChild('Property');
                $nodeProperty->addAttribute('Id', $propertyId);
                $nodeProperty->addAttribute('ValueId', $propertyValue);
            }
        }

        if (!empty($selected) && !isset($current['quantity'])) {
            foreach ($selected as $configurationId => $quantity) {
                $nodeSelected = $xmlParams->addChild('Selected');
                $nodeSelected->addAttribute('ConfigurationId', $configurationId);
                $nodeSelected->addAttribute('Quantity', $quantity);
            }
        }

        return $xmlParams->asXML();
    }

    private function definePageOg($product)
    {
        if (!empty($product->title)){
            General::$_page['og']['title'] = $product->title;
        }
        if (!empty($product->mainPicture['medium'])){
            General::$_page['og']['image'] = $product->mainPicture['medium'];
        }
    }
}
