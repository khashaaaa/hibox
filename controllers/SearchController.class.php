<?php

class SearchController extends GeneralContoller
{

    const MODULE_REVIEWS = 'Reviews';

    private $defaultPerPage = 20;

    public function requestEmptyAction()
    {
        $crumbs = $this->getDefaultCrubms();
        CrumbsController::setCrumbs($crumbs);

        return $this->render('controllers/search/request-empty');
    }

    public function reviewsAction()
    {
        $this->request->set('module', self::MODULE_REVIEWS);
        return $this->defaultAction();
    }

    public function vendorAction($alias = null)
    {
        $id = $this->request->request('id');
        if ($alias !== null) {
            $vendorRepository = new VendorRepository($this->cms);
            list($id, $needRedirect) = $vendorRepository->parseVendorIdFromAlias($alias);
            if ($needRedirect) {
                return $this->redirect($id);
            }
        }

        $this->request->set('vid', $id);
        return $this->defaultAction();
    }

    public function brandAction($id = null)
    {
        if ($id == null) {
            $id = $this->request->getValue('id');
        }
        $this->request->set('brand', $id);
        return $this->defaultAction();
    }

    public function categoryAction($alias = null)
    {
        if ($alias == null) {
            $alias = $this->request->request('cid');
        }

        $seoRepository = new SeoCategoryRepository($this->cms);
        list($cid, $needRedirect) = $seoRepository->parseCategoryIdFromAlias($alias);
        if ($needRedirect) {
            RequestWrapper::LocationRedirect($cid);
        }

        $cid = $cid ? $cid : $alias;

        $this->request->set('cid', $cid);
        return $this->defaultAction();
    }

    public function defaultAction()
    {
        // сохраняем некоторые выбранные пользователем параметры поиска
        $this->setCookieToDisplayParams($this->request);

        if ($this->request->getMethod() == 'POST' && $this->request->isAjax()) {
            // для ajax запросов отдаем контент поиска в json формате
            return $this->ajaxSearch();
        } elseif ($this->request->getMethod() == 'POST') {
            // редирект на постоянный урл с GET параметрами
            $this->baseUrl = $this->prepareBaseUrl($this->request);
            return $this->request->LocationRedirect($this->baseUrl->Get());
        } elseif (
            !$this->request->getValue('module') &&
            !$this->request->getValue('vid') &&
            !$this->request->getValue('brand') &&
            !$this->request->getValue('cid') &&
            !$this->request->getValue('search') &&
            !$this->request->getValue('imageId')
        ) {
            // если нет параметров поиска - отобразить отдельную страницу
            return $this->requestEmptyAction();
        }

        return $this->getSearchPage($this->request);
    }

    // запоминаем какие "фильтры" выбраны при поиске
    private function setCookieToDisplayParams($request)
    {
        if ($request->getValue('Provider')) {
            $this->getUser()->setProvider($request->getValue('Provider'));
        }
        if ($request->getValue('layout')) {
            Cookie::set('layout-items', $request->getValue('layout'));
        } elseif (! Cookie::get('layout-items')) {
            Cookie::set('layout-items', 'block');
        }
    }

    private function prepareBaseUrl($request)
    {
        if ($request->post('search'))
            $this->baseUrl->DeleteKey('search')->Add('search', $request->post('search'));
        if ($request->post('searchInner'))
            $this->baseUrl->DeleteKey('search')->Add('search', trim($request->post('search') . ' ' . $request->post('searchInner')));
        if ($request->post('vid'))
            $this->baseUrl->DeleteKey('vid')->Add('vid', $request->post('vid'));
        if ($request->post('brand'))
            $this->baseUrl->DeleteKey('brand')->Add('brand', $request->post('brand'));
        if ($request->post('cid'))
            $this->baseUrl->DeleteKey('cid')->Add('cid', $request->post('cid'));
        if ($request->post('imageId'))
            $this->baseUrl->DeleteKey('imageId')->Add('imageId', $request->post('imageId'));
        return $this->baseUrl;
    }

    private function getDefaultCrubms()
    {
    	return array(
			array('title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()),
    		array('title' => Lang::get('search_results')),
    	);
    }

    private function getSearchPage($request)
    {
        $title = null;
        $crumbs = $this->getDefaultCrubms();
        // по умолчанию страница открывается без результатов поиска,
        // но админ может включить отображение товаров при первом открытии страницы
        if (General::getConfigValue('search_items_at_render_page')) {
            $search = $this->getSearchContent();
            if (isset($search['redirect'])) {
                return $this->request->LocationRedirect($search['redirect']);
            }
            if (isset($search['info']['crumbs'])) {
            	$crumbs = $search['info']['crumbs'];
            }
            $searchContent = $search['content'];
            $searchContentInfo = $search['contentInfo'];
            $info = isset($search['info']) ? $search['info'] : array();
            if (isset($info['title'])) {
                $title = $info['title'];
            }
        } else {
            $info = $this->getSearchContentInfo();
            if (isset($info['crumbs'])) {
            	$crumbs = $info['crumbs'];
            }
            if (isset($info['title'])) {
                $title = $info['title'];
            }
            $searchContent = null;
            $searchContentInfo = $this->fetchSearchContentInfo($info);
            if (isset($info['title'])) {
                $title = $info['title'];
            }
        }

        CrumbsController::setCrumbs($crumbs);
        $this->definePageOg($info);
        $this->pageSetSeoData($info);
        return $this->render('controllers/search/index', [
            'searchContent' => $searchContent,
            'searchContentInfo' => $searchContentInfo,
            'searchContentInfoTitle' => $title,
        ]);
    }

    public function ajaxSearch()
    {
        $search = $this->getSearchContent();

        return $this->sendAjaxResponse($search);
    }

    public function getSearchContent()
    {
        $lang = Session::getActiveLang();
        $sid = $this->getUser()->getSid();

        $searchResult = new OtapiBatchItemSearchResult(null);
        $searchProperties = array();
        $availableSearchMethodList = array();
        $activeSearchMethod = new OtapiProviderSearchMethodInfo(null);
        $products = array();
        $categories = array();
        $subCategories = array();
        $hintCategories = array();
        $info = array();
        $title = null;

        // обработка параметров поиска (пагинация, кол-во на страницу, тип шаблона и подобное)
        $searchParams = $this->bindSearchParams($this->request);

        try {
            // формируем xml для сервисов
            $xmlParams = $this->generateSearchXML($searchParams);

            $framePosition = $searchParams['filter']['from'];
            $frameSize = $searchParams['filter']['perPage'];

            // поиск товаров
            /** @var OtapiBatchItemSearchResultAnswer $searchResult */
            OTAPILib2::BatchSearchItemsFrame(
                $lang,
                $sid,
                $xmlParams,
                $framePosition,
                $frameSize,
                'SubCategories,HintCategories,Vendor,Brand,Category,RootPath,SearchProperties,AvailableSearchMethods',
                $searchResult
            );
            OTAPILib2::makeRequests();

            $info = $this->getSearchInfoBySearchResult($searchResult);
            $searchContentInfo = $this->fetchSearchContentInfo($info);
            if (isset($info['title'])) {
                $title = $info['title'];
            }
            // для виртуальной категории отдельная логика
            if ($this->showAsVirtual($searchResult)) {
                return array(
                    'content' => $searchContentInfo . General::runBlock('SubCategoryNew'),
                    'contentInfo' => $searchContentInfo,
                    'contentCrumbs' => $this->fetchSearchCrumbs($info['crumbs']),
                    'url' =>  $this->baseUrl->Get(),
                    'info' => $info,
                    'searchContentInfoTitle' => $info['title'],
                );
            }

            if ($searchResult && $searchResult->GetResult()->GetItems()) {
                // если сервисы сообщили что необходимо открыть карточку товара
                if ($searchResult->GetResult()->GetItems()->IsFoundByItemId()) {
                    foreach ($searchResult->GetResult()->GetItems()->GetItems()->GetContent()->GetItem() as $value) {
                        $url = UrlGenerator::generateItemUrl($value->GetId(), array('isAbsolute' => true));
                        return array('redirect' => $url);
                    }
                }

                // для удобства разбираем ответ сервисов по переменным
                $searchResult = $searchResult->GetResult();
                /** @var OtapiBatchItemSearchResult $searchResult */
                $result = $searchResult->GetItems();
                foreach ($result->GetItems()->GetContent()->GetItem() as $value) {
                    $products[] = Product::getObject($value->GetId(), $value, array(
                        'category' => $searchResult->GetCategory(),
                    ));
                }
                $searchParams['Provider'] = InstanceProvider::getObject()->GetAliasByProviderName(
                    Session::getActiveLang(),
                    $searchResult->GetItems()->GetProvider()
                );
                $searchParams['SearchMethod'] = $searchResult->GetItems()->GetSearchMethod();
                $searchParams['filter']['perPage'] = $searchResult->GetItems()->GetCurrentFrameSize();
                foreach ($result->GetCategories()->GetContent()->GetItem() as $value) {
                    if (!$value->IsFiltered() && !$value->IsHidden()) {
                        $categories[] = $value;
                    }
                }
                if ($searchResult->GetSubCategories()) {
                    foreach ($searchResult->GetSubCategories()->GetContent()->GetItem() as $value) {
                        if (!$value->IsFiltered() && !$value->IsHidden()) {
                            $subCategories[] = $value;
                        }
                    }
                }
                if ($searchResult->GetHintCategories()) {
                    foreach ($searchResult->GetHintCategories()->GetContent()->GetItem() as $value) {
                        if (!$value->IsFiltered() && !$value->IsHidden()) {
                            $hintCategories[] = $value;
                        }
                    }
                }

                if ($searchResult->GetAvailableSearchMethods()) {
                    foreach ($searchResult->GetAvailableSearchMethods()->GetContent()->GetItem() as $value) {
                        $availableSearchMethodList[] = $value;

                        // определяем активный способ поиска
                        if (
                            $searchResult->GetItems()->GetProvider() == $value->GetProvider() &&
                            $searchResult->GetItems()->GetSearchMethod() == $value->GetSearchMethod()
                        ) {
                            $activeSearchMethod = $value;
                        }
                    }
                }

                if ($searchResult->GetSearchProperties()) {
                    $searchProperties = $this->prepareSearchProperties(
                        $searchResult->GetSearchProperties(),
                        $searchParams,
                        $activeSearchMethod
                    );
                }

                // проверяем наличие ошибок в батче
                if (OTBase::isTest()) {
                    $this->searchResultRegisterError($searchResult);
                }
            }
            $this->baseUrl->Set($this->getRequestUrl());

            //throw ServiceException::generateTestServiceException();
        } catch(Exception $e) {
            if (OTBase::isTest()) {
                //$this->errorHandler->registerError($e);
            }

            $error = $this->errorHandler->getExceptionAsArray($e);
            return array(
                'content' => $this->renderPartial('controllers/search/search-error', [
                    'message' => $error['message'],
                ]),
                'contentInfo' => array(),
                'error' => $error,
                'url' =>  $this->baseUrl->Get(),
            );
        }

        // регистрируем все категориия для генерации алиасов
        UrlGenerator::addCategoriesForWarmup(array_merge($categories, $subCategories, $hintCategories));

        if (!$searchResult->GetVendor()->GetId() && !$searchResult->GetBrand()->GetId()) {
            $contentInfo = $this->fetchSearchContentInfo($info);
        } else {
            $contentInfo = "";
        }

        $content = $this->renderPartial('controllers/search/search-content', [
            'searchResult' => $searchResult,
            'searchProperties' => $searchProperties,
            'availableSearchMethodList' => $availableSearchMethodList,
            'activeSearchMethod' => $activeSearchMethod,
            'products' => $products,
            'categories' => $categories,
            'subCategories' => $subCategories,
            'hintCategories' => $hintCategories,
            'searchParams' => $searchParams,
            'baseUrl' => $this->baseUrl,
            'contentInfo' => $contentInfo,
            'searchContentInfoTitle' => $title,
        ]);

        return array(
            'content' => $content,
            'contentInfo' => $contentInfo,
        	'contentCrumbs' => $this->fetchSearchCrumbs($info['crumbs']),
            'url' =>  $this->baseUrl->Get(),
            'info' => $info,
            'searchContentInfoTitle' => $title,
        );
    }

    private function searchResultRegisterError($searchResult)
    {
        $errorMethods = array(
            'GetItemsError',
            'GetSubCategoriesError',
            'GetRootPathError',
            'GetSearchPropertiesError',
            'GetVendorError',
            'GetBrandError',
            'GetCategoryError',
            'GetHintCategoriesError',
            'GetAvailableSearchMethodsError',
        );
        foreach ($errorMethods as $method) {
            /** @var OtapiBatchErrorInfoOfGeneralErrorCode $batchError */
            $batchError = $searchResult->$method();
            if ($batchError->GetErrorDescription()) {
                $e = new Exception('BatchError' . $method . ': ' . $batchError->GetErrorDescription());
                $this->errorHandler->registerError($e);
            }
        }
    }

    private function fetchSearchCrumbs($crumbs) {
        return $this->renderPartial('controllers/crumbs/crumbs', [
            'crumbs' => $crumbs,
        ]);
	}

    private function getRequestUrl()
    {
        $requestData = $this->request->getAll();
        if ($this->request->request('module')) $requestData['module'] = $this->request->request('module');
        if ($this->request->request('vid')) $requestData['vid'] = $this->request->request('vid');
        if ($this->request->request('brand')) $requestData['brand'] = $this->request->request('brand');
        if ($this->request->request('cid')) $requestData['cid'] = $this->request->request('cid');
        if ($this->request->request('search')) $requestData['search'] = $this->request->request('search');

        $url = UrlGenerator::generateSearchUrlByParams($requestData, array('isAbsolute' => true));

        if (isset($requestData['module'])) {
            unset($requestData['module']);
        }
        if (isset($requestData['alias'])) {
            unset($requestData['alias']);
        }
        if (isset($requestData['vid'])) {
            unset($requestData['vid']);
            if (isset($requestData['id']))
                unset($requestData['id']);
        }
        if (isset($requestData['brand'])) {
            unset($requestData['brand']);
            if (isset($requestData['id']))
                unset($requestData['id']);
        }
        if (isset($requestData['cid'])) {
            unset($requestData['cid']);
            if (isset($requestData['id']))
                unset($requestData['id']);
        }
        if (isset($requestData['search'])) {
            unset($requestData['search']);
        }

        if (isset($requestData['action'])) unset($requestData['action']);
        if (isset($requestData['q'])) unset($requestData['q']);
        if (CMS::IsFeatureEnabled('Seo2')) {
            if (isset($requestData['p'])) unset($requestData['p']);
        }

        // добавим в урл поисковые параметры если они не были добавлены ранее
        if (!empty($requestData)) {
            $addParams = array();
            foreach ($requestData as $paramName => $paramValue) {
                if (strpos($url, '?' . $paramName . '=') === false && strpos($url, '&' . $paramName . '=') === false) {
                    $addParams[$paramName] = $paramValue;
                }
            }
            if (!empty($addParams)) {
                $url .= (strpos($url, '?') === false) ? '?' : '&';
                $url .= http_build_query($addParams);
            }
        }

        return $url;
    }

    private function generateSearchXML($searchParams)
    {
        $xmlParams = new SimpleXMLElement('<SearchItemsParameters></SearchItemsParameters>');
        if (! empty($searchParams['searchWord'])) {
            $xmlParams->ItemTitle = $searchParams['searchWord'];
        }
        if (! empty($searchParams['Provider'])) {
            $xmlParams->Provider = $searchParams['Provider'];
        }
        if (! empty($searchParams['SearchMethod'])) {
            $xmlParams->SearchMethod = $searchParams['SearchMethod'];
        }
        if (! empty($searchParams['module'])) {
            $xmlParams->Module = $searchParams['module'];
        }
        if (! empty($searchParams['vid'])) {
            $xmlParams->VendorId = $searchParams['vid'];
        }
        if (! empty($searchParams['brand'])) {
            $xmlParams->BrandId = $searchParams['brand'];
        }
        if (! empty($searchParams['cid'])) {
            $xmlParams->CategoryId = $searchParams['cid'];
        }
        if (! empty($searchParams['imageId'])) {
            $xmlParams->ImageFileId = $searchParams['imageId'];
        }
        if (! empty($searchParams['StuffStatus'])) {
            $xmlParams->StuffStatus = $searchParams['StuffStatus'];
        }
        if (! empty($searchParams['features'])) {
            $featuresXml = $xmlParams->addChild('Features');
            foreach ($searchParams['features'] as $feature => $value) {
                if (!empty($value)) {
                    $el = $featuresXml->addChild('Feature', $value);
                    $el->addAttribute('Name', $feature);
                }
            }
        }

        if (! empty($searchParams['filter']['sortBy'])) {
            $xmlParams->OrderBy = $searchParams['filter']['sortBy'];
        }
        

        if(! empty($searchParams['filter']['minPrice'])) {
            //Если не задана настройками цены
            $xmlParams->MinPrice = $searchParams['filter']['minPrice'];
        }
        if (! empty($searchParams['filter']['maxPrice'])) {
            $xmlParams->MaxPrice = $searchParams['filter']['maxPrice'];
        }
        if (isset($xmlParams->MinPrice) || isset($xmlParams->MaxPrice)) {
            $xmlParams->CurrencyCode = User::getObject()->getCurrencyCode();
        }

        // фильтр по минимальной партии
        if (! empty($searchParams['filter']['minFirstLot'])) {
            $xmlParams->MinFirstLot = $searchParams['filter']['minFirstLot'];
        }
        if (! empty($searchParams['filter']['maxFirstLot'])) {
            $xmlParams->MaxFirstLot = $searchParams['filter']['maxFirstLot'];
        }

        // фильтр по рейтингу продавца
        if (! empty($searchParams['filter']['minRating'])) {
            $xmlParams->MinVendorRating = $searchParams['filter']['minRating'];
        }
        if (! empty($searchParams['filter']['maxRating'])) {
            $xmlParams->MaxVendorRating = $searchParams['filter']['maxRating'];
        }

        // фильтр по кол-ву продаж
        if (! empty($searchParams['filter']['minVolume'])) {
            $xmlParams->MinVolume = $searchParams['filter']['minVolume'];
        }
        if (! empty($searchParams['filter']['maxVolume'])) {
            $xmlParams->MaxVolume = $searchParams['filter']['maxVolume'];
        }

        if (! empty($searchParams['filter']['filters'])) {
            $configuratorsXml = $xmlParams->addChild('Configurators');
            foreach ($searchParams['filter']['filters'] as $pid => $vid) {
                if ($vid && $pid != 'StuffStatus') {
                    if (is_array($vid)) {
                        foreach ($vid as $key => $p) {
                            $el = $configuratorsXml->addChild('Configurator');
                            $el->addAttribute('Pid', $pid);
                            $el->addAttribute('Vid', $key);
                        }
                    } else {
                        $el = $configuratorsXml->addChild('Configurator');
                        $el->addAttribute('Pid', $pid);
                        $el->addAttribute('Vid', $vid);
                    }
                } elseif ($pid == 'StuffStatus' && $vid) {
                    $xmlParams->addChild('StuffStatus', $vid);
                }
            }
        }
        // игнорировать frameSize и выдавать количество товаров согласно OptimalFrameSize для способа поиска
        if (! empty($searchParams['filter']['useOptimalFrameSize']) && $searchParams['filter']['useOptimalFrameSize']) {
            $xmlParams->UseOptimalFrameSize = 'true';
        }

        return $xmlParams->asXML();
    }

    /**
     * формируем массив с параметрами поиска, обрабатываем значения по умолчанию
     *
     * @param RequestWrapper $request
     */
    public function bindSearchParams($request)
    {
        $providerAlias = $this->getUser()->getProvider();
        // определить провадйера автоматически для vid, brand, cid
        if ($request->request('vid') || $request->request('brand') || $request->request('cid')) {
            $providerAlias = null;
        }
        if ($request->post('Provider')) {
            $providerAlias = $request->post('Provider');
        } elseif ($request->get('Provider')) {
            $providerAlias = $request->get('Provider');
        }

        $searchWord = urldecode($request->request('search'));
        $searchWord = mb_convert_encoding($searchWord, 'UTF-8', 'UTF-8');

        $cost = $request->request('cost', array());
        $firstLotRange = $request->request('firstLotRange', array());
        $rating = $request->request('rating', array());
        $count = $request->request('count', array());
        $perPage = $request->request('per_page', $this->defaultPerPage);
        $useOptimalFrameSize = ($request->request('per_page')) ? false : true;

        $searchParams = array(
            'layout' => $request->request('layout') ? $request->request('layout') : Cookie::get('layout-items'),
            'SearchMethod' => $request->request('SearchMethod') ? $request->request('SearchMethod') : '',
            'module' => $request->request('module') ? $request->request('module') : '',
            'vid' => $request->request('vid') ? $request->request('vid') : '',
            'brand' => $request->request('brand') ? $request->request('brand') : '',
            'cid' => $request->request('cid') ? $request->request('cid') : '',
            'searchWord' => $searchWord,
            'imageId' => $request->request('imageId') ? $request->request('imageId') : '',
            'StuffStatus' => $request->request('StuffStatus'),
            'features' => $request->request('features') ? $request->request('features') : '',
            'filter' => array(
                'from' => $request->request('from', 0),
                'perPage' => $perPage,
                'useOptimalFrameSize' => $useOptimalFrameSize,
                'sortBy' => $request->request('sort_by') ? $request->request('sort_by') : '',
                'minPrice' => ! empty($cost['from']) ? $cost['from'] : 0,
                'maxPrice' => ! empty($cost['to']) ? $cost['to'] : 0,
                'minFirstLot' => ! empty($firstLotRange['from']) ? $firstLotRange['from'] : '',
                'maxFirstLot' => ! empty($firstLotRange['to']) ? $firstLotRange['to'] : '',
                'minRating' => ! empty($rating['from']) ? $rating['from'] : '',
                'maxRating' => ! empty($rating['to']) ? $rating['to'] : '',
                'minVolume' => ! empty($count['from']) ? $count['from'] : '',
                'maxVolume' => ! empty($count['to']) ? $count['to'] : '',
                'filters' => $request->request('filters') ? $request->request('filters') : '',
            )
        );
        if ($providerAlias) {
            $searchParams['Provider'] = InstanceProvider::getObject()->GetProviderNameByAlias(Session::getActiveLang(), $providerAlias);
        }

        return $searchParams;
    }

    // Подготавливаем массив фильтров
    private function prepareSearchProperties($searchProperties, $searchParams, $activeSearchMethod)
    {
        $resultProperties = array();

        if (!$activeSearchMethod->Configurators()) {
            return $resultProperties;
        }

        $typeOfInput = $activeSearchMethod->GetMultipleConfiguratorLogic() == 'None' ? 'radio' : 'checkbox';

        /** @var DataListOfOtapiItemSearchProperty $searchProperties */
        foreach ($searchProperties->GetContent()->GetItem() as $property) {
            $tmpProperty = array(
                'id' => $property->GetId(),
                'name' => $property->GetName(),
                'isCategory' => $property->IsCategory(),
                'isBrand' => $property->IsBrand(),
                'values' => array(
                    'active' => array(),
                    'other' => array()
                )
            );

            // просматриваем xml напрямую, минуя создание объектов otapilib2/types/*
            if (empty($property->GetRawData()->Values->PropertyValue)) {
                continue;
            }
            foreach ($property->GetRawData()->Values->PropertyValue as $value) {
                if (empty($value->Id)) {
                    continue;
                }

                $id = (string)$value->Id;
                $name = isset($value->Name) ? (string)$value->Name : '';
                $itemCount = isset($value->ItemCount) ? (string)$value->ItemCount : '';
                $filterUrl = clone $this->baseUrl;

                $tmpValue = array(
                    'id' => $id,
                    'name' => $name,
                    'itemCount' => $itemCount
                );

                if ($typeOfInput == 'checkbox') {
                    if (!empty($searchParams['filter']['filters']) &&
                        !empty($searchParams['filter']['filters'][(string)$property->GetId()]) &&
                        !empty($searchParams['filter']['filters'][(string)$property->GetId()][$id])) {
                        $tmpValue['link'] = $filterUrl->DeleteKey(array('filters' , $property->GetId(), $id))->Get();
                        $tmpProperty['values']['active'][] = $tmpValue;
                    } else {
                        $tmpValue['link'] = $filterUrl->Add('filters[' . $property->GetId() . '][' . $id . ']', $id)->Get();
                        $tmpProperty['values']['other'][] = $tmpValue;
                    }
                } else {
                    if (! empty($searchParams['filter']['filters'][$property->GetId()]) && $id == $searchParams['filter']['filters'][$property->GetId()]) {
                        $tmpValue['link'] = $filterUrl->DeleteKey(array('filters' , $property->GetId()))->Get();
                        $tmpProperty['values']['active'][] = $tmpValue;
                    } else {
                        $tmpValue['link'] = $filterUrl->Add('filters[' . $property->GetId() . ']', $id)->Get();
                        $tmpProperty['values']['other'][] = $tmpValue;
                    }
                }
            }

            if (! empty($searchParams['filter']['filters'][$property->GetId()])) {
                $resultProperties['active'][] = $tmpProperty;
            } else {
                $resultProperties['other'][] = $tmpProperty;
            }
        }

        return $resultProperties;
    }

    private function fetchSearchContentInfo($info)
    {
        return $this->renderPartial('controllers/search/search-content-info', [
            'info' => $info,
        ]);
    }

    private function getSearchInfoBySearchResult($searchResult)
    {
        $cid = null;
        if ($this->request->request('module')) {
            return $this->getModuleInfoAttributes();
        } elseif ($this->request->request('vid')) {
            // TODO: в ответе $searchResult уже есть информация о продавце, не нужно делать лишний вызов OTAPILib2
            return $this->getVendorInfoAttributes();
        } elseif ($this->request->request('brand')) {
            $info = array();

            if ($this->request->request('cid')) {
                $cid = $this->request->request('cid');
            }

            if ($searchResult && $searchResult->GetResult()->GetBrand()) {
                $info['title'] = $searchResult->GetResult()->GetBrand()->GetName();
                $info['img'] = $searchResult->GetResult()->GetBrand()->GetPictureUrl();
                $info['description'] = $searchResult->GetResult()->GetBrand()->GetDescription();
                $info['crumbs'] = array(
                    array('title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()),
                    array('title' => Lang::get('all_brands'), 'url' => UrlGenerator::toRoute('brands')),
                    array('title' => Lang::get('Brand') . ' ' .  $searchResult->GetResult()->GetBrand()->GetName(), 'url' => UrlGenerator::generateSearchUrlByParams(['brand' => $searchResult->GetResult()->GetBrand()->GetId()]))
                );
                if ($cid !== null) {
                    $info['crumbs'][] = array('title' => $searchResult->GetResult()->GetCategory()->GetName(), 'url' => UrlGenerator::generateSearchUrlByParams(['cid' => $cid]));
                }
                if($this->request->request('search')) {
                    $info['crumbs'][] = array('title' => Lang::get('search_results_on_request') . " " . htmlspecialchars($this->request->request('search')));
                }
            }
            $seoRepository = new SeoCategoryRepository($this->cms);
            $seoData = $seoRepository->getCategorySEO($searchResult->GetResult()->GetBrand()->GetId(), Session::getActiveLang());
            if (empty($seoData['pagetitle'])) {
                $seoData['pagetitle'] = $info['title'];
            }
            $this->pageCategorySetSeoData($seoData);

            $lastCrumb = array_pop($info['crumbs']);
            unset($lastCrumb['url']);
            $info['crumbs'][] = $lastCrumb;

            $info['canonicalUrl'] = UrlGenerator::generateSearchUrlByParams(['brand' => $this->request->request('brand')]);

            return $info;
        } elseif ($this->request->request('cid')) {
            $info = array(
                'description' => General::getSeoText($this->request->request('cid'), Session::getActiveLang()),
            );

            General::$_page['seo_description'] = $info['description'];
            if ($searchResult && $searchResult->GetResult()->GetCategory()) {
                $info['title'] = $searchResult->GetResult()->GetCategory()->GetName();
                $info['img'] = $searchResult->GetResult()->GetCategory()->GetIconImageUrl();
                if (!$this->request->request('search')) {
                    $info['crumbs'] = CrumbsController::generateCrumbsByRootPath($searchResult->GetResult()->GetRootPath()->GetContent());
                } else {
                    $info['crumbs'] = CrumbsController::generateCrumbsByRootPath($searchResult->GetResult()->GetRootPath()->GetContent(), true);
                    $info['crumbs'][] = array('title' => Lang::get('search_results_on_request') . ' "' . urldecode($this->request->request('search')) . '"');
                }

                $seoRepository = new SeoCategoryRepository($this->cms);
                $seoData = $seoRepository->getCategorySEO($searchResult->GetResult()->GetCategory()->GetId(), Session::getActiveLang());
                if (empty($seoData['pagetitle'])) {
                    $seoData['pagetitle'] = $info['title'];
                }
                $this->pageCategorySetSeoData($seoData);
            }

            $info['canonicalUrl'] = UrlGenerator::generateSearchUrlByParams(['cid' => $this->request->request('cid')]);

            return $info;
        } elseif ($this->request->request('imageId')) {
            $info = array();
            $info['crumbs'] = array(
                array('title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()),
                array('title' => Lang::get('photo_search'))
            );
            return $info;
        } elseif ($this->request->request('search')) {
			return $this->getSimpleSearchInfoAttributes();
        } else {
            return array();
        }
    }

    /**
     *
     * @param OtapiBatchItemSearchResultAnswer $searchResult
     *
     * @return boolean
     */
    private function showAsVirtual($searchResult)
    {
        if (!$searchResult) {
            return false;
        }
        if ($searchResult->GetResult()->GetCategory()->IsVirtual()) {
            return true;
        }
        if ($searchResult->GetResult()->GetCategory()->GetAvailableItemRatingListContentTypes()) {
            foreach ($searchResult->GetResult()->GetCategory()->GetAvailableItemRatingListContentTypes()->GetContentType() as $contentType) {
                if ($contentType == 'Item') {
                    return true;
                }
            }
        }
        return false;
    }

    private function getSearchContentInfo()
    {
        if ($this->request->request('module')) {
            return $this->getModuleInfoAttributes();
        } elseif ($this->request->request('vid')) {
            return $this->getVendorInfoAttributes();
        } elseif ($this->request->request('brand')) {
            return $this->getBrandInfoAttributes();
        } elseif ($this->request->request('cid')) {
            return $this->getCategoryInfoAttributes();
        } elseif ($this->request->request('search')) {
        	return $this->getSimpleSearchInfoAttributes();
        } else {
            return array();
        }
    }

    private function getModuleInfoAttributes()
    {
        if ($this->request->request('module') == self::MODULE_REVIEWS) {
            General::$_page['title'] =  Lang::get('with_reviews');

            return array(
                'title' => General::$_page['title'],
                'description' => '',
                'img' => '',
                'crumbs' => array(
                    array('title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()),
                    array('title' => General::$_page['title']),
                ),
            );
        }

        return array();
    }

    private function getSimpleSearchInfoAttributes()
    {
        General::$_page['title'] =  Lang::get('search_results_on_request') . ' "' . urldecode($this->request->request('search')) . '"';

		return array(
			'title' => '',
    		'description' => '',
    		'img' => '',
    		'crumbs' => array(
				array('title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()),
    			array('title' => Lang::get('search_results_on_request') . ' "' . urldecode($this->request->request('search')) . '"'),
    		),
		);
    }

    private function getVendorInfoAttributes()
    {
        OTAPILib2::GetVendorInfo(Session::getActiveLang(), $this->request->request('vid'), $vendorInfo);
        OTAPILib2::makeRequests();
        $vendorInfo = $vendorInfo->GetVendorInfo();
        $seoRepository = new SeoCategoryRepository($this->cms);
        $seoData = $seoRepository->getVendorSEO($this->request->request('vid'), Session::getActiveLang());
        if (empty($seoData['pagetitle'])) {
            $seoData['pagetitle'] = General::getConfigValue('seller_prefix') . $vendorInfo->GetDisplayName() . General::getConfigValue('seller_suffix');
        }
        $this->pageCategorySetSeoData($seoData);

        $info = array(
            'title' => $vendorInfo->GetDisplayName() ? $vendorInfo->GetDisplayName() : '',
            'description' => '',
            'img' => $vendorInfo->GetDisplayPictureUrl() ? $vendorInfo->GetDisplayPictureUrl() : '',
            'crumbs' => array(
                array('title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()),
                array(
                    'title' => $vendorInfo->GetDisplayName() ? Lang::get('vendor') . ' ' .  $vendorInfo->GetDisplayName() : Lang::get('vendor') . ' ' .  $this->request->request('vid'),
                    'url' => UrlGenerator::generateSearchUrlByParams(['vid' => $this->request->request('vid')])
                    )
            )
        );


        if ($this->request->request('cid')) {
            $info['crumbs'] = $this->addCrumbsCategoryName($info['crumbs'], $this->request->request('cid'));
        }
        if ($this->request->request('search')) {
            $info['crumbs'][] = array('title' => Lang::get('search_results_on_request') . " " . htmlspecialchars($this->request->request('search')));
        }

        $lastCrumb = array_pop($info['crumbs']);
        unset($lastCrumb['url']);
        $info['crumbs'][] = $lastCrumb;

        $info['canonicalUrl'] = UrlGenerator::generateSearchUrlByParams(['vid' => $this->request->request('vid')]);

        return $info;
    }

    private function getBrandInfoAttributes()
    {
        $info = array();

        try {
            $brandInfo = null;
            OTAPILib2::GetBrandInfo(Session::getActiveLang(), RequestWrapper::getValueSafe('brand'), $brandInfo);

            OTAPILib2::makeRequests();
            if ($brandInfo && $brandInfo->GetBrandInfo()) {
                $info['title'] = $brandInfo->GetBrandInfo()->GetName();
                $info['img'] = $brandInfo->GetBrandInfo()->GetPictureUrl();
                $info['description'] = $brandInfo->GetBrandInfo()->GetDescription();
                $info['crumbs'] = array(
                	array('title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()),
                	array('title' => Lang::get('all_brands'), 'url' => UrlGenerator::toRoute('brands')),
                	array('title' => Lang::get('Brand') . ' ' . $brandInfo->GetBrandInfo()->GetName(), 'url' => UrlGenerator::generateSearchUrlByParams(['brand' => $brandInfo->GetBrandInfo()->GetId()]))
                );
                if ($this->request->request('cid')) {
                    $info['crumbs'] = $this->addCrumbsCategoryName($info['crumbs'], $this->request->request('cid'));
                }
                if($this->request->request('search')) {
                    $info['crumbs'][] = array('title' => Lang::get('search_results_on_request') . " " . htmlspecialchars($this->request->request('search')));
                }
                $seoRepository = new SeoCategoryRepository($this->cms);
                $seoData = $seoRepository->getCategorySEO($brandInfo->GetBrandInfo()->GetId(), Session::getActiveLang());
                if (empty($seoData['pagetitle'])) {
                    $seoData['pagetitle'] = $info['title'];
                }
                $this->pageCategorySetSeoData($seoData);
            }
        } catch(Exception $e) {
            $this->errorHandler->registerError($e);
        }

        $lastCrumb = array_pop($info['crumbs']);
        unset($lastCrumb['url']);
        $info['crumbs'][] = $lastCrumb;

        $info['canonicalUrl'] = UrlGenerator::generateSearchUrlByParams(['brand' => $this->request->request('brand')]);

        return $info;
    }

    private function addCrumbsCategoryName($crumbs, $cid) {
        $lang = Session::getActiveLang();
        $categoryName = null;
        OTAPILib2::GetCategoryInfo($lang, $cid, $request);
        OTAPILib2::makeRequests();
        $categoryName = $request->GetOtapiCategory()->GetName();
        $crumbs[] = array('title' => $categoryName, 'url' => null);
        return $crumbs;
    }

    private function getCategoryInfoAttributes()
    {
        try {
            $lang = Session::getActiveLang();
            $sid = $this->getUser()->getSid();
            // обработка параметров поиска (пагинация, кол-во на страницу, тип шаблона и подобное)
            $searchParams = $this->bindSearchParams($this->request);

            // формируем xml для сервисов
            $xmlParams = $this->generateSearchXML($searchParams);

            $framePosition = $searchParams['filter']['from'];
            $frameSize = $searchParams['filter']['perPage'];

            // поиск товаров
            OTAPILib2::BatchSearchItemsFrame(
                $lang,
                $sid,
                $xmlParams,
                $framePosition,
                $frameSize,
                'ExceptSearchItems,Category,RootPath',
                $searchResult
            );
            OTAPILib2::makeRequests();
            $info = $this->getSearchInfoBySearchResult($searchResult);
        } catch(Exception $e) {
            $this->errorHandler->registerError($e);
        }

        return $info;
    }

    private function pageCategorySetSeoData($seoData)
    {
        $prefix = General::getConfigValue('category_prefix', '');
        $suffix = General::getConfigValue('category_suffix', '');
        if (! empty($seoData['seo_title']) && $seoData['seo_title'] != '||') {
            list($prefix, $suffix) = explode('||', $seoData['seo_title']);
        }

        if (!empty($seoData['pagetitle'])) {
            General::$_page['title'] = $seoData['pagetitle'];
            if ($prefix) General::$_page['title'] = $prefix . ' ' . General::$_page['title'];
            if ($suffix) General::$_page['title'] = General::$_page['title'] . ' ' . $suffix;
        }
        if (!empty($seoData['seo_keywords'])) {
            General::$_page['seo_keywords'] = $seoData['seo_keywords'];
        }
        if (!empty($seoData['seo_description'])) {
            General::$_page['seo_description'] = $seoData['seo_description'];
        }
    }

    private function pageSetSeoData($info)
    {
        if (isset($info['canonicalUrl'])) {
            General::$_page['meta']['canonical'] = $info['canonicalUrl'];
        }

        if ($this->request->request('module')) {
            // TODO:
        } elseif ($this->request->request('vid')) {
            // TODO:
        } elseif ($this->request->request('brand')) {
            $prefix = General::getConfigValue('brand_prefix', '');
            $suffix = General::getConfigValue('brand_suffix', '');
            if (!empty($info['title'])) {
                if (empty(General::$_page['title'])) {
                    General::$_page['title'] = $info['title'];
                }
                if ($prefix) General::$_page['title'] = $prefix.' '.General::$_page['title'];
                if ($suffix) General::$_page['title'] = General::$_page['title'].' '.$suffix;
            }
            if (!empty($info['seo_keywords'])) {
                General::$_page['seo_keywords'] = $info['seo_keywords'];
            }
            if (!empty($info['seo_description'])) {
                General::$_page['seo_description'] = $info['seo_description'];
            }
        } elseif ($this->request->request('cid')) {
            // TODO:
        } elseif ($this->request->request('search')) {
            // TODO:
        } elseif ($this->request->request('imageId')) {
            if (!empty($info['title'])) {
                General::$_page['title'] = $info['title'];
            } else {
                General::$_page['title'] = Lang::get('search_results');
            }
        } else {
            // TODO:
        }
    }

    private function definePageOg($info)
    {
        if (!empty($info['title'])){
            General::$_page['og']['title'] = $info['title'];
        }
        if (!empty($info['description'])){
            General::$_page['og']['description'] = substr(strip_tags($info['description']), 0, 160);
        }
        if (!empty($info['img'])){
            General::$_page['og']['image'] = $info['img'];
        }
    }
}
