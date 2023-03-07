<?php

ini_set('memory_limit', '1024M');

class ItemListNew extends GenerateBlock
{
    
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'itemlistnew'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';
    protected $multiSearchCache = true;

    /**
     * @var SearchPropNew
     */
    private $searchProp;

    /**
     * @var UrlWrapper
     */
    private $baseUrl;
    /**
     * @var UrlWrapper
     */
    private $urlWithoutSearch;
    /**
     * @var UrlWrapper
     */
    private $urlWithoutCategoryId;
     
    /**
     * @var VendorRepository
     */
    private $vendorRepository;
    
    /**
     * @var $isVendor
     */
    private $isVendor;
    
    /**
     * @var $searchParametersHash
     */
    private $searchParametersHash;

    /**
     * @var InstanceProvider
     */
    private $instanceProvider;

    const PER_PAGE_COUNT_0  = 0;
    const PER_PAGE_COUNT_4  = 4;
    const PER_PAGE_COUNT_8  = 8;
    const PER_PAGE_COUNT_16 = 16;
    const PER_PAGE_COUNT_20 = 20;
    const PER_PAGE_COUNT_40 = 40;
    const PER_PAGE_COUNT_50 = 50;
    const PER_PAGE_COUNT_100 = 100;
    const PER_PAGE_COUNT_200 = 200;


    public function __construct() {
        parent::__construct(true);
        $this->otapilib->setErrorsAsExceptionsOn();

        $this->baseUrl = new UrlWrapper();
        $this->baseUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

        $this->clearUrl = new UrlWrapper();
        $this->clearUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

        $this->urlWithoutSearch = new UrlWrapper();
        $this->urlWithoutSearch->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

        $this->urlWithoutCategoryId = new UrlWrapper();
        $this->urlWithoutCategoryId->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

        $this->searchProp = new SearchPropNew();

        $this->instanceProvider = InstanceProvider::getObject();
    }

    /**
     * @return bool
     */
    public function isSearchMulti()
    {
        return !(RequestWrapper::get('Provider', false) && RequestWrapper::get('SearchMethod', false));
    }

    private function generateSearchBlocks()
    {
        $blockList = array('SubCategories', 'Vendor', 'RootPath', 'Category', 'SearchProperties');

        if (RequestWrapper::getValueSafe('brand')) {
            $blockList[] = 'Brand';
        }

        return implode(',', $blockList);
    }

    private function redirectToAliasIfNeed()
    {
        $cid = $this->request->get('cid');
        if (
            !$this->request->isPost() &&
            ($this->request->request('p') == 'search' || $this->request->request('q') == 'search') &&
            !$this->request->request('search') &&
            !$this->request->request('imageId') &&
            !empty($cid)
        ) {
            if ( in_array('Seo2', General::$enabledFeatures)) {
                $seoCatModel = new SeoCategoryRepository(new CMS());
                $alias = $seoCatModel->getCategoryAlias($cid);
                $url = '/subcategory/' . htmlspecialchars($alias, ENT_QUOTES);
            } else {
                $url = '/?p=subcategory&cid=' . $cid;
            }
            $this->request->LocationRedirect($url);
        }
    }

    protected function setVars()
    {
        /* Используется для ссылок на категорию из админки */
        $this->redirectToAliasIfNeed();

        Session::clearError();
        $this->resetParamsIfNeed();

        if ($this->needAjaxTemplate()) {
            return $this->setTemplate('itemlistnew_ajax');
        }
        if (RequestWrapper::getParamExists('isAjax')) {
            return $this->getCountOfItemsByFilter();
        }
    
        $this->assignCategoryId();
        $сategoryInfo = $this->prepareBaseUrl();
        // если категория виртуальная - в ней нет товаров, её надо открыть как виртуальную со списком категорий
        if ($this->showAsVirtual($сategoryInfo)) {
            $сategoryInfo['isvirtual'] = 'true';
            $this->request->LocationRedirect(UrlGenerator::generateSubcategoryUrl($сategoryInfo, true));
        }
        // определяем картинку категории для старого шаблона
        if (! empty($сategoryInfo['IconImageUrl'])) {
            $GLOBALS['CategoryImage'][$сategoryInfo['Id']] = $сategoryInfo['IconImageUrl'];
        }

        $categoryItemFilter = $this->searchParams();
        $categoryItemFilter = $this->searchParamsAddited($categoryItemFilter);
        $this->prepareSearchParametersHash($categoryItemFilter);
        $searchTypes = $this->getSearchMethods($categoryItemFilter);
        $categoryItemFilter = $this->searchParamsAddProvider($categoryItemFilter, $searchTypes, $сategoryInfo);
        $newCategoryFilter = Plugins::invokeEvent('newCategoryFilterXML', array('xml' =>$categoryItemFilter));
        if ($newCategoryFilter)
            $categoryItemFilter = $newCategoryFilter;

        $perPage = $this->getAndAssignPerPageItemCount($categoryItemFilter);
        $from = $this->getAndAssignSearchOffset();
        $this->getAndAssignPerPageList($categoryItemFilter);
        
        $this->getCurrentSearchType($сategoryInfo, $searchTypes);

        $this->getIsProviderActive($сategoryInfo);
        $this->isVendor = RequestWrapper::getParamExists('id') || RequestWrapper::getParamExists('vid');
        $this->tpl->assign('isVendor', $this->isVendor);        
        if ($this->isVendor) {
            $vid = RequestWrapper::getParamExists('id') ? RequestWrapper::getValueSafe('id') : RequestWrapper::getValueSafe('vid');

            $vendorInfo = $this->otapilib->GetVendorInfo($vid);
            $sid = Session::getUserOrGuestSession();
            $vendors = $this->otapilib->GetFavoriteVendors($sid);
            if (is_array($vendors['elements']) && count($vendors['elements']) > 0) {
                foreach ($vendors['elements'] as $vendor) {
                    if ($vendor['itemid'] == $vendorInfo['id']) {
                        $vendorInfo['favoriteItemId'] = $vendor['id'];
                    }
                }
            }
            $this->tpl->assign('vendorInfo', $vendorInfo);
        }        
       
        $isSearching = $this->CheckSearch();
        if ($isSearching['search']) {            
            if ($this->CheckMultiSearch($сategoryInfo)) {
                if($this->fileMysqlMemoryCache->Exists('multi_search:' . $this->searchParametersHash)){
                    $foundAllMulti = json_decode($this->fileMysqlMemoryCache->GetCacheEl('multi_search:' . $this->searchParametersHash), true);
                }
                else{
                    $foundAllMulti = $this->getMultiSearchItems($categoryItemFilter,$searchTypes);
                    if($this->multiSearchCache)
                        $this->fileMysqlMemoryCache->AddCacheEl('multi_search:' . $this->searchParametersHash, 600, json_encode($foundAllMulti));
                }
            } else {
                $foundAll = $this->getSimpleItems($categoryItemFilter,$from,$perPage,$searchTypes);
            }
            $this->getAndAssignSearchTypesInfo();
        } else {
            $this->tpl->assign('no_search_request', true);
            $this->tpl->assign('no_search_request_reason', $isSearching['reason']);
        }


        if (isset($foundAll)) {
            $this->setSimpleSearch($foundAll, $searchTypes);
            $this->tpl->assign('searchProperties', isset($foundAll['SearchProperties']) ? $foundAll['SearchProperties'] : array());
        }
        if (isset($foundAllMulti) && !(isset($foundAll) && $foundAll === false)) {
            $searchResults = 0;
            foreach($foundAllMulti as $providerSearchResult){
                $searchResults += is_array($providerSearchResult) ? count($providerSearchResult) : 0;
            }
            if($searchResults)
                $this->setMultiSearch($foundAllMulti);
        }

        $this->prepareHintCats();
        $this->tpl->assign('checkMultiSearch', $this->CheckMultiSearch($сategoryInfo));
        return true;
    }

    private function getSimpleItems($categoryItemFilter,$from,$perPage,$SearchTypes){
        $this->otapilib->setErrorsAsExceptionsOn();
        $foundAll = false;
        $searchData = array();
        $N_search = new SimpleXMLElement($categoryItemFilter);
        if (! $this->isVendor && ! empty($SearchTypes)) { 
            foreach ($SearchTypes as $type) {            
                if (($type['Provider']==$N_search->Provider) && ($type['SearchMethod']==$N_search->SearchMethod)){
                    $searchData = $type;
                    break;
                }
            }
        }
        try {
            $foundAll = $this->otapilib->BatchSearchItemsFrame(User::getObject()->getSid(), $categoryItemFilter, $from, $perPage, $this->generateSearchBlocks());

            if ($this->isVendor) {                
                foreach ($SearchTypes as $type) {            
                    if (($type['Provider']==$foundAll['Items']['Provider']) && ($type['SearchMethod']==$foundAll['Items']['SearchMethod'])){
                        $searchData = $type;
                        break;
                    }
                }
                $foundAll['searchData'] = $searchData;
            } else {
                $foundAll['searchData'] = $searchData;
            }
            
        }
        catch (ServiceException $e) {
            if ((string)$e->getErrorCode() != 'NotFound') {
                Session::setError($e->getErrorMessage(), $e->getErrorCode(), $e->getSubErrorCode());
            }
        }
        catch(Exception $e){
            Session::setError($e->getMessage(), $e->getCode());
        }
        return $foundAll;

    }

    private function getMultiSearchItems($categoryItemFilter,$SearchTypes)
    {
        $foundAll = array();
        $isItems = false;
        $errorStorage = array();
        try {
            if (CFG_MULTI_CURL)  {
                $useBatch = true;
                $this->otapilib->InitMulti();
                foreach ($SearchTypes as $type) {                    
                    if ($type['Provider'].'_'.$type['SearchMethod'] != 'ProductComments_Default') {
                        $categoryItemFilterTmp = $this->searchParamsAddMulti($categoryItemFilter,$type['Provider'],$type['SearchMethod']);
                        if ($useBatch) {
                            $this->otapilib->BatchSearchItemsFrame(User::getObject()->getSid(), $categoryItemFilterTmp, 0, $this->getPerPageMulti($type['Provider'].'_'.$type['SearchMethod']), $this->generateSearchBlocks());
                            $useBatch = false;
                        } else {
                            $this->otapilib->SearchItemsFrame($categoryItemFilterTmp, 0, $this->getPerPageMulti($type['Provider'].'_'.$type['SearchMethod']));
                        }
                    }
                }
                $this->otapilib->MultiDo();
            }
            $batchData = null;
            $this->otapilib->setErrorsAsExceptionsOn();
            foreach ($SearchTypes as $type) {
                $categoryItemFilterTmp = $this->searchParamsAddMulti($categoryItemFilter,$type['Provider'],$type['SearchMethod']);
                $ProviderNew = $type['Provider'];
                $SearchMethodNew = $type['SearchMethod'];
                try {
                    if (! $batchData) {
                        $batchData = $this->otapilib->BatchSearchItemsFrame(User::getObject()->getSid(), $categoryItemFilterTmp, 0, $this->getPerPageMulti($type['Provider'].'_'.$type['SearchMethod']), $this->generateSearchBlocks());
                        $foundAll[$ProviderNew.'_'.$SearchMethodNew] = $batchData;
                    } else {
                        $foundAll[$ProviderNew.'_'.$SearchMethodNew] = $batchData;
                        $foundAll[$ProviderNew.'_'.$SearchMethodNew]['Items'] = $this->otapilib->SearchItemsFrame($categoryItemFilterTmp, 0, $this->getPerPageMulti($type['Provider'].'_'.$type['SearchMethod']));
                    }
                    $isItems = true;
                } catch (ServiceException $e) {
                    if((string)$e->getErrorCode() != 'NotFound') {
                        $foundAll[$ProviderNew.'_'.$SearchMethodNew] = false;
                        throw new ServiceException('SearchItemsFrame[' . $ProviderNew.'_'.$SearchMethodNew . ']', array(), $e->getMessage(), $e->getErrorCode(), null, $e->getSubErrorCode());
                    }
                }
            }
            $this->otapilib->setErrorsAsExceptionsOff();
            if (CFG_MULTI_CURL)  {
                $this->otapilib->StopMulti();
            }
        }
        catch (ServiceException $e) {
            if ((string)$e->getErrorCode() != 'NotFound') {
                $this->multiSearchCache = false;
                $errorStorage[] = $e;
            }
        }
        catch (Exception $e) {
            Session::setError($e->getMessage(), $e->getCode());
        }
        if (! $isItems) {
            foreach($errorStorage as $e) {
                Session::setError($e->getErrorMessage(), $e->getErrorCode());
            }
        }
        return $foundAll;
    }

    private function setSimpleSearch($foundAll, $SearchTypes){
        if ((! empty($foundAll['Items']['IsFoundByItemId'])) && ($foundAll['Items']['IsFoundByItemId'] == 'true')) {
            RequestWrapper::LocationRedirect(UrlGenerator::generateItemUrl($foundAll['Items']['Items']['data'][0]['Id'], array()));
        }
        // если категория виртуальная - в ней нет товаров, её надо открыть как виртуальную со списком категорий
        if ($this->showAsVirtual($foundAll['Category'])) {
            $foundAll['Category']['isvirtual'] = 'true';
            $this->request->LocationRedirect(UrlGenerator::generateSubcategoryUrl($foundAll['Category'], true));
        }

        $this->assignGlobals($foundAll);
        $this->prepareSubCategories($foundAll);
        $this->prepareCategories($foundAll);
        $this->prepareItemList($foundAll, $SearchTypes);
        $this->prepareSearchProp($foundAll);
    }

    private function setMultiSearch($foundAllMulti) {
        foreach ($foundAllMulti as $itemList) {
            if ((! empty($itemList['Items']['IsFoundByItemId'])) && ($itemList['Items']['IsFoundByItemId'] == 'true')) {
                RequestWrapper::LocationRedirect(UrlGenerator::generateItemUrl($itemList['Items']['Items']['data'][0]['Id'], array()));
                break;
            }
        }
        $this->checkManyProviders($foundAllMulti);
        $this->assignGlobalsMulti($foundAllMulti);
        $this->prepareSubCategoriesMulti($foundAllMulti);
        $this->prepareCategoriesMulti($foundAllMulti);
        $this->prepareItemListMulti($foundAllMulti);
        $this->prepareSearchPropMulti($foundAllMulti);
    }

    private function showAsVirtual($сategoryInfo)
    {
        if (isset($сategoryInfo['IsVirtual']) && $сategoryInfo['IsVirtual'] === 'true') {
            return true;
        }
        if (isset($сategoryInfo['AvailableItemRatingListContentTypes'])) {
            foreach ($сategoryInfo['AvailableItemRatingListContentTypes'] as $contentType) {
                if ($contentType == 'Item') {
                    return true;
                }
            }
        }
        return false;
    }

    private function resetParamsIfNeed(){

        if (!$this->request->valueExists('clear')) return ;

        $this->baseUrl->DeleteKey('rating')
            ->DeleteKey('cost')
            ->DeleteKey('filters')
            ->DeleteKey('script_name')
            ->DeleteKey('clear')
            ->DeleteKey('ignorefilters');

        $this->request->LocationRedirect($this->baseUrl->Get());
    }



    private function needAjaxTemplate(){
        return $this->request->get('p') != 'item_list_ajax' && defined('CFG_AJAX_ITEM_LIST');
    }

    private function getAndAssignPerPageItemCount($categoryItemFilter){
        $default_perpage = self::PER_PAGE_COUNT_0;

        $searchXml = simplexml_load_string($categoryItemFilter);
        $defaultPerpage = General::getNumConfigValue('default_perpage', self::PER_PAGE_COUNT_20);

        $key = (string)$searchXml->Provider . '_' . (string)$searchXml->SearchMethod;
        switch ($key) {
            case 'Taobao_Official':
                $default_perpage = $this->getCookiePerPage($key . '_perPage', General::getNumConfigValue('oficial_catalog_perpage', $defaultPerpage));
                break;
            case 'Taobao_Extended':
                $default_perpage = $this->getCookiePerPage($key . '_perPage', General::getNumConfigValue('extended_catalog_perpage', $defaultPerpage));
                break;
            case 'Taobao_ExtendedNew':
                $default_perpage = $this->getCookiePerPage($key . '_perPage', General::getNumConfigValue('extendedNew_catalog_perpage', $defaultPerpage));
                break;
            case 'Warehouse_Default':
                $default_perpage = $this->getCookiePerPage($key . '_perPage', General::getNumConfigValue('warehouse_catalog_perpage', $defaultPerpage));
                break;
            case 'ProductComments_Default':
                $default_perpage = $this->getCookiePerPage($key . '_perPage', General::getNumConfigValue('comments_catalog_perpage', $defaultPerpage));
                break;
            case 'Kitmall_Default':
                $default_perpage = $this->getCookiePerPage($key . '_perPage', General::getNumConfigValue('oficial_catalog_perpage', $defaultPerpage));
                if ($default_perpage > self::PER_PAGE_COUNT_50 ) {
                    $default_perpage = self::PER_PAGE_COUNT_50;
                }
                break;
                
            default:
                $default_perpage = $this->getCookiePerPage($key . '_perPage', $defaultPerpage);
                break;
        }
        if ($this->request->getValue('per_page')) {
            $this->setCookiePerPage($key . '_perPage', $this->request->getValue('per_page'));
            $perpage = $this->request->getValue('per_page');
        } else {
            $perpage = $default_perpage;
        }
        $this->tpl->assign('perpage', $perpage);
        return $perpage;
    }

    private function getAndAssignSearchOffset(){
        $from = intval($this->request->get('from'));
        $this->tpl->assign('from', $from);
        return $from;
    }

    private function getAndAssignPerPageList ($categoryItemFilter) {
        $searchXml = simplexml_load_string($categoryItemFilter);
        $provider = (string)$searchXml->Provider;
        $perPage = array(20, 40, 100);        
        $additionalPerPage = false; 
        if ($provider == 'Warehouse' && General::getNumConfigValue('warehouse_catalog_perpage')) {
            $additionalPerPage = General::getNumConfigValue('warehouse_catalog_perpage');
        }
        
        if ($provider != 'Warehouse' && General::getNumConfigValue('extended_catalog_perpage')) {
            $additionalPerPage = General::getNumConfigValue('extended_catalog_perpage');
        }
        
        if ($additionalPerPage) {
            if (! in_array($additionalPerPage, $perPage)) {
                $perPage[] = $additionalPerPage;
            }
            asort($perPage);
        }
        $this->tpl->assign('pp', $perPage);
        return $perPage;
    }


    private function assignCategoryId(){
        $this->tpl->assign('cid', $this->request->getValueSafe('cid'));
    }

    private function getPerPageMulti($key){
        switch ($key) {
            case 'Taobao_Official': // Официальный поиск - Tmall
                $perPage = General::getNumConfigValue('tmall_search_perpage', self::PER_PAGE_COUNT_4);
                break;
            case 'Taobao_Extended': // Товары из Китая
                $perPage = General::getNumConfigValue('simple_search_perpage', self::PER_PAGE_COUNT_8);
                break;
            case 'Taobao_ExtendedNew': // Товары из Китая
                $perPage = General::getNumConfigValue('simple_searchNew_perpage', self::PER_PAGE_COUNT_8);
                break;
            case 'Warehouse_Default': // Товары со склада
                $perPage = General::getNumConfigValue('warehouse_search_perpage', self::PER_PAGE_COUNT_4);
                break;
            case 'Taobao_Promoted': // Рекомендации таобао
                $perPage = General::getNumConfigValue('promoted_search_perpage', self::PER_PAGE_COUNT_4);
                break;
            case 'ProductComments_Default':
                $perPage = General::getNumConfigValue('comments_search_perpage', self::PER_PAGE_COUNT_8);
                break;
            default:
                $perPage = self::PER_PAGE_COUNT_20;
                break;
        }
        return $perPage;
    }



    private function prepareBaseUrl()
    {
        $this->urlWithoutCategoryId->DeleteKey('cid');
        $url = parse_url($this->urlWithoutCategoryId->Get());
        $query = array();
        if (! empty($url['query'])) {
            parse_str($url['query'], $query);
        }

        $prevCategoryLink = null;
        $cid = RequestWrapper::getRequestValueSafe('cid');
        try {
            $categoryInfo = array();
            $catpath = ! empty($GLOBALS['rootpath']) ? $GLOBALS['rootpath'] :
                ($cid ? $this->otapilib->GetCategoryRootPath($cid) : null);
            if (is_array($catpath)) {
                $categoryInfo = array_pop($catpath);
            }
        } catch(ServiceException $e){
            Session::setError($e->getErrorMessage(), $e->getErrorCode());
        }
        if (! empty($catpath) && is_array($catpath)) {
            array_pop($catpath);
            $prevCrumb = array_pop($catpath);
            if (! empty($prevCrumb)) {
                if (isset($query['p'])) {
                    unset($query['p']);
                }
                if (isset($prevCrumb['IsVirtual'])) {
                    unset($query['search']);
                    unset($query['cost']);
                    unset($query['filters']);
                }
                if ($prevCrumb['isparent'] == 'false') {
                    $prevCategoryLink = General::generateUrl('category', array_merge($query, $prevCrumb));
                } else {
                    $prevCategoryLink = General::generateUrl('subcategory', array_merge($query, $prevCrumb, array('root' => count($catpath) == 1)));
                }
                if (! empty($query)) {
                    $prevCategoryLink .= (strpos($prevCategoryLink, '?') !== false ? '&' : '?') . http_build_query($query);
                }
                $url2 = parse_url($prevCategoryLink);
            }
        }

        $this->baseUrl->DeleteKey('tmall')->DeleteKey('new_prod')->DeleteKey('Discount');
        $this->clearUrl->DeleteKey('from')
            ->DeleteKey('tmall')
            ->DeleteKey('cost')
            ->DeleteKey('filters')
            ->DeleteKey('new_prod');

        if($this->request->get('p_ajax'))
            $this->baseUrl->DeleteKey('p')->DeleteKey('p_ajax')->Add('p', $this->request->get('p_ajax'));

        if($this->request->post('sort_by'))
            $this->baseUrl->DeleteKey('sort_by')->Add('sort_by', $this->request->post('sort_by'));
        if($this->request->post('per_page'))
            $this->baseUrl->DeleteKey('per_page')->Add('per_page', $this->request->post('per_page'))
                ->DeleteKey('from')->Add('from', 0);
        if($this->request->post('search'))
            $this->baseUrl->DeleteKey('search')->Add('search', $this->request->post('search'));
        if($this->request->post('imageId'))
            $this->baseUrl->DeleteKey('imageId')->DeleteKey('search')->Add('imageId', $this->request->post('imageId'));
        if($this->request->post('searchInner'))
            $this->baseUrl->DeleteKey('search')->Add('search', $this->request->post('search') . ' ' . $this->request->post('searchInner'));
        if($this->request->post('cid'))
            $this->baseUrl->DeleteKey('cid')->Add('cid', $this->request->post('cid'));
        if($this->request->post('vid'))
            $this->baseUrl->DeleteKey('vid')->Add('vid', $this->request->post('vid'));
        if($this->request->post('brand'))
            $this->baseUrl->DeleteKey('brand')->Add('brand', $this->request->post('brand'));
        if($this->request->get('tmall'))
            $this->baseUrl->Add('tmall', 'true');
        if($this->request->get('Discount'))
            $this->baseUrl->Add('Discount', 'true');
        if($this->request->post('Provider'))
            $this->baseUrl->DeleteKey('Provider')->Add('Provider', $this->request->post('Provider'));
        if($this->request->post('SearchMethod'))
            $this->baseUrl->DeleteKey('SearchMethod')->Add('SearchMethod', $this->request->post('SearchMethod'));

        if ($this->request->getMethod() == 'POST') {

            $this->request->LocationRedirect($this->baseUrl->Get());
        }

        $this->baseUrl->DeleteKey('from');
        $this->urlWithoutSearch->DeleteKey('search');

        $this->tpl->assign('urlWithoutCategoryId', $this->urlWithoutCategoryId->Get());
        $this->tpl->assign('urlWithoutSearch', $this->urlWithoutSearch->Get());
        $this->tpl->assign('baseUrl', $this->baseUrl);
        $this->tpl->assign('clearUrl', $this->clearUrl->Get());
        $this->tpl->assign('prevCategoryLink', $prevCategoryLink);
        return $categoryInfo;
    }

    private function searchParams()
    {
        $xmlParams = new SimpleXMLElement('<SearchItemsParameters></SearchItemsParameters>');

        $xmlSearchConfig = simplexml_load_file(CFG_APP_ROOT.'/config/request2xml.search.xml');
        foreach($xmlSearchConfig->predefined_paramters->parameter as $c)
            $xmlParams->addChild((string)$c['name'], (string)$c[0]);

        foreach($xmlSearchConfig->parameter as $c)
            $this->appendXmlParameter($c->children(),(string)$c['name'],$xmlParams);

        if (defined('CFG_SEARCH_LANG')) {
            $xmlParams->addChild('LanguageOfQuery', CFG_SEARCH_LANG);
        }

        self::prepareFiltersXml($xmlParams);
        self::prepareFeaturesXml($xmlParams);
        $this->addPredefinedCategoryModeToSearchXML($xmlParams);
        return $xmlParams->asXML();
    }

    public static function prepareFeaturesXml(&$xmlElement){
        if (isset($_GET['Discount'])) {
            $configuratorsXml = $xmlElement->addChild('Features');
            $el =$configuratorsXml->addChild('Feature', 'true');
            $el->addAttribute('Name', 'Discount');
        }
    }


    public function checkManyProviders($foundAllMulti){
        $countProviders = array();
        foreach ($foundAllMulti as $key=>$list) {
            if (!isset($list['Items']['Items']['totalcount']))
                continue;
            if ($list['Items']['Items']['totalcount']>0)
                $countProviders[] = explode("_", $key);
        }
        if (count($countProviders)==1 && !RequestWrapper::get('SearchMethod', false)) {
            $this->request->LocationRedirect($this->baseUrl->Add('Provider', $this->instanceProvider->GetAliasByProviderName(Session::getActiveLang(), $countProviders[0][0]))->Add('SearchMethod', $countProviders[0][1])->Get());
        }
    }


    public static function prepareFiltersXml(&$xmlElement){
        if (isset($_GET['filters'])) {
            $configuratorsXml = $xmlElement->addChild('Configurators');
            foreach ($_GET['filters'] as $pid => $vid) {
                if ($vid && $pid!='StuffStatus'){
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
                }
                elseif($pid=='StuffStatus' && $vid){
                    $xmlElement->addChild('StuffStatus', $vid);
                }
            }
        }
    }

    private function appendXmlParameter($requestKeys, $xmlKey, &$xmlElement){
        $value = $this->getArrayValueByKeys($this->request->getAll(), $requestKeys);
        if($value)
            $xmlElement->addChild($xmlKey, $this->request->escapeValue($value));
    }

    private function getArrayValueByKeys($array, $keys){
        $tmp = $array;
        foreach($keys->request as $k){
            $tmp = @$tmp[(string)$k];
        }
        return $tmp;
    }

    private function addPredefinedCategoryModeToSearchXML($xml){
        $search_category_mode = General::getConfigValue('search_category_mode') ?
            General::getConfigValue('search_category_mode') : 'External';
        $xml->CategoryMode = (string)$search_category_mode;
    }

    private function searchParamsAddited($oldxml){
        $N_search = new SimpleXMLElement($oldxml);

        if (General::getConfigValue('min_cost_goods')) {
            if ($N_search->MinPrice<General::getConfigValue('min_cost_goods')) {
                $N_search->MinPrice = General::getConfigValue('min_cost_goods');
            }
            if ((isset($N_search->MaxPrice)) and ($N_search->MaxPrice<General::getConfigValue('min_cost_goods'))) {
                $N_search->MaxPrice = General::getConfigValue('min_cost_goods');
            }
        }
        if (General::getConfigValue('hide_bu_goods')) {
            $N_search->StuffStatus = 'New';
        }

        if (isset($N_search->MinPrice) || isset($N_search->MaxPrice)) {
            $N_search->CurrencyCode = User::getObject()->getCurrencyCode();
        }

        return $N_search->asXML();
    }

    private function searchParamsAddProvider($oldxml, $searchTypes, $categoryInfo = array()){
        $N_search = new SimpleXMLElement($oldxml);
        if(isset($N_search->VendorId) && !empty($N_search->VendorId)){
            return $N_search->asXML();
        }        
        if (RequestWrapper::getValueSafe('Provider') && RequestWrapper::getValueSafe('SearchMethod')) {
            $GLOBALS['activeProviderName'] = '';
            foreach($searchTypes as $type) {
                if ((RequestWrapper::getValueSafe('Provider') == $type['Provider']) && (RequestWrapper::getValueSafe('SearchMethod') == $type['SearchMethod']))
                    $GLOBALS['activeProviderName'] = $type['DisplayName'];
                    break;
            }
            //Если в ссылке указан провайдер и метод берем их
            $alias = RequestWrapper::getValueSafe('Provider');
            $provider = $this->instanceProvider->GetProviderNameByAlias(Session::getActiveLang(), $alias);
            $searchMethod = RequestWrapper::getValueSafe('SearchMethod');
        } else {
            //Если нет в ссылке провайдера то берем первый из списка всех методо
            if (! empty($searchTypes[0])) {
                $provider = $searchTypes[0]['Provider'];
                $searchMethod = $searchTypes[0]['SearchMethod'];
            }
        }
        if (! empty($provider)) {
            if (($provider=='China') && (strpos($searchMethod, 'Other') !== false)) {
                $N_search->Provider = 'Taobao';
                $N_search->SearchMethod = str_replace('Other', 'Extended', $searchMethod);
            } elseif ($provider=='China') {
                $N_search->Provider = 'Taobao';
                $N_search->SearchMethod = $searchMethod;
            } else {
                $N_search->Provider = $provider;
                if (isset($searchMethod)) {
                    $N_search->SearchMethod = $searchMethod;
                }
            }
        }

        return $N_search->asXML();
    }

    private function searchParamsAddMulti($oldxml,$Provider,$SearchMethod){
        $N_search = new SimpleXMLElement($oldxml);
        if (! isset($N_search->VendorId)) {
            $N_search->Provider = $Provider;
            $N_search->SearchMethod = $SearchMethod;
        }
        return $N_search->asXML();
    }

    private function assignPrevNextMeta($foundAll) 
    {
        if (!empty($foundAll['Items']['CurrentFrameSize']) && !empty($foundAll['Items']['MaximumPageCount'])) {
            $url = clone $this->baseUrl;

            $maxPageCount = $foundAll['Items']['MaximumPageCount'];
            $pageSize = $foundAll['Items']['CurrentFrameSize'];
            $from = $this->getAndAssignSearchOffset();
            $currentPage = floor($from / $pageSize) + 1;
            if ($currentPage != 1) {
                if ($currentPage == 2) {
                    $GLOBALS['prev'] = $url->DeleteKey('from')->Get();
                } else {
                    $GLOBALS['prev'] = $url->DeleteKey('from')->Add('from', $from - $pageSize)->Get();
                }
            }
            if ($maxPageCount > 1 && ! ($currentPage >= $maxPageCount)) {
                $GLOBALS['next'] = $url->DeleteKey('from')->Add('from',$from + $pageSize)->Get();
            }
        }
    }
    
    private function assignGlobals($foundAll){
        $GLOBALS['rootpath'] = isset($foundAll['RootPath']) && is_array($foundAll['RootPath']) ? array_reverse($foundAll['RootPath']) : array();
        $GLOBALS['categoryInfo'] = end($GLOBALS['rootpath']);
        if (@$foundAll['Items']['TranslatedItemTitle'])
            $GLOBALS['TranslatedItemTitle'] = $foundAll['Items']['TranslatedItemTitle'];

        if(RequestWrapper::getValueSafe('brand'))
            $GLOBALS['brandinfo'] = $foundAll['Brand'];

        $this->assignPrevNextMeta($foundAll);
    }

    private function getIsProviderActive($сategoryInfo) {
        $IsProvider = '' ;
        if ((RequestWrapper::getValueSafe('Provider')) || (RequestWrapper::getValueSafe('id')) || (!General::getConfigValue('use_multi_search')) || (! empty($сategoryInfo['SearchMethod'])))   {
            $IsProvider = 'active';
        }
        $this->tpl->assign('IsProvider', $IsProvider);
    }

    private function assignGlobalsMulti($foundAll){
        foreach($foundAll as $found){
            if($found){
                $firstSearch = $found;
                break;
            }
        }
        $GLOBALS['rootpath'] = isset($firstSearch['RootPath']) && is_array($firstSearch['RootPath']) ? array_reverse($firstSearch['RootPath']) : array();
        if(!isset($GLOBALS['categoryInfo']))
            $GLOBALS['categoryInfo'] = end($GLOBALS['rootpath']);
        foreach ($foundAll as $one) {
            if (@$one['Items']['TranslatedItemTitle'])
                $GLOBALS['TranslatedItemTitle'] = $one['Items']['TranslatedItemTitle'];
            if(RequestWrapper::getValueSafe('brand') && @$one['Brand'])
                $GLOBALS['brandinfo'] = @$one['Brand'];
        }
        
        $this->assignPrevNextMeta($foundAll);
    }

    private function prepareSubCategories($foundAll){
        $subCategories = ! empty($foundAll['SubCategories']) ? $foundAll['SubCategories'] : array();
        
        if($this->cms->IsFeatureEnabled('Seo2') && is_array($subCategories)){
            try {
                $SeoCatsRepository = new SeoCategoryRepository(new CMS());
                foreach($subCategories as &$c){
                    $c['alias'] = $SeoCatsRepository->getCategoryAlias(@$c['Id'], @$c['Name']);
                }
            } catch (DBException $e) {
                Session::setError($e->getMessage(), 'DBError');
            }

        }
        $this->tpl->assign('subCategories', $subCategories);
    }
    
    private function prepareCategories($foundAll){
        $categories = isset($foundAll['Items']['Categories']) ? $foundAll['Items']['Categories'] : array();

        if($this->cms->IsFeatureEnabled('Seo2') && is_array($categories)){
            try {
                $SeoCatsRepository = new SeoCategoryRepository(new CMS());
                foreach($categories as &$c){
                    $c['alias'] = $SeoCatsRepository->getCategoryAlias(@$c['Id'], @$c['Name']);
                }
            } catch (DBException $e) {
                Session::setError($e->getMessage(), 'DBError');
            }

        }
        $this->tpl->assign('categories', $categories);
    }
    
    //Теперь нам нужен лишь список доступных по параметрам
    private function getProviderSearchMethodInfoList($xmlSearchParametrs)
    {        
        $this->otapilib->setErrorsAsExceptionsOn();
        try{
            $cacheExists = $this->fileMysqlMemoryCache->Exists('ItemListNewGetProviderSearchMethodInfoList:id' . Session::getActiveLang() . ':' . $this->searchParametersHash);
            if($cacheExists){
                $SearchTypesXML = $this->fileMysqlMemoryCache->GetCacheEl('ItemListNewGetProviderSearchMethodInfoList:id' . Session::getActiveLang() . ':' . $this->searchParametersHash);
            }
            else{
                $this->otapilib->setResultInXMLOn();
                $SearchTypesXML = $this->otapilib->GetAvailableProviderSearchMethodInfoListForSearchParameters($xmlSearchParametrs);
                $this->otapilib->setResultInXMLOff();
                $SearchTypesXML = $SearchTypesXML->asXML();
                $this->fileMysqlMemoryCache->AddCacheEl('ItemListNewGetProviderSearchMethodInfoList:id' . Session::getActiveLang() . ':' . $this->searchParametersHash, 3600, $SearchTypesXML);
            }

            $searchTypes =  $this->otapilib->GetAvailableProviderSearchMethodInfoListForSearchParameters(array(), $SearchTypesXML);
            return $searchTypes;

        }
        catch(ServiceException $e){
            if((string)$e->getErrorCode() != 'NotFound')
                Session::setError($e->getMessage(), $e->getErrorCode());
        }
        catch(Exception $e){
            Session::setError($e->getMessage(), $e->getCode());
        }
    }

    private function getSearchMethods($categoryItemFilter)
    {
        $SearchTypes = $this->getProviderSearchMethodInfoList($categoryItemFilter);
        //более не нужно подменять
        $allFeatures = array();
        if ($categoryItemFilter) {
            $N_search = new SimpleXMLElement($categoryItemFilter);
        }

        if (! empty($SearchTypes)) {
            foreach($SearchTypes as $SearchType){
                foreach($SearchType['Features'] as $feature) {
                    $allFeatures[] = $feature['Name'];
                }
            }
            foreach($SearchTypes as $k => $SearchType){
                $SearchTypes[$k]['Alias'] = $this->instanceProvider->GetAliasByProviderName(Session::getActiveLang(), $SearchTypes[$k]['Provider']);
            }
        }
        
        $this->tpl->assign('SearchTypes', $SearchTypes);
        $this->tpl->assign('allFeatures', $allFeatures);
        return $SearchTypes;
    }

    private function getCurrentSearchType($сategoryInfo, $searchTypes)
    {
        $CurrentSearchType = false;
        if ($this->request->get('Provider') && $this->request->get('SearchMethod')) {
            $CurrentSearchType = $this->instanceProvider->GetProviderNameByAlias(Session::getActiveLang(), $this->request->get('Provider')) . '_' . $this->request->get('SearchMethod');
        } elseif (! $this->CheckMultiSearch($сategoryInfo) && ! empty($searchTypes)) {
            $CurrentSearchType = $searchTypes[0]['Provider'] . '_' . $searchTypes[0]['SearchMethod'];
        }
        $this->tpl->assign('сategoryInfo', $сategoryInfo);
        $this->tpl->assign('CurrentSearchType', $CurrentSearchType);
    }

    private function prepareSubCategoriesMulti($foundAll){
        $subCategories = array();
        foreach ($foundAll as $one) {
            $subCategories = array_merge((array)$subCategories, isset($one['SubCategories']) ? (array)$one['SubCategories'] : array());            
        }

        if($this->cms->IsFeatureEnabled('Seo2') && is_array($subCategories)){
            try {
                $SeoCatsRepository = new SeoCategoryRepository(new CMS());
                foreach($subCategories as &$c){
                    $c['alias'] = $SeoCatsRepository->getCategoryAlias(@$c['Id'], @$c['Name']);
                }
            } catch (DBException $e) {
                Session::setError($e->getMessage(), 'DBError');
            }

        }
        $this->tpl->assign('subCategories', array_unique($subCategories,SORT_REGULAR));
    }
    
    private function prepareCategoriesMulti($foundAll){
        $categories = array();
        foreach ($foundAll as $one) {
            $categories = array_merge((array)$categories, (array)$one['Items']['Categories']);
        }

        if($this->cms->IsFeatureEnabled('Seo2') && is_array($categories)){
            try {
                $SeoCatsRepository = new SeoCategoryRepository(new CMS());
                foreach($categories as &$c){
                    $c['alias'] = $SeoCatsRepository->getCategoryAlias(@$c['Id'], @$c['Name']);
                }
            } catch (DBException $e) {
                Session::setError($e->getMessage(), 'DBError');
            }

        }
        $this->tpl->assign('categories', array_unique($categories,SORT_REGULAR));
    }
    

    private function prepareItemList($foundAll, $searchTypes){
        $itemList = isset($foundAll['Items']['Items']) ? $foundAll['Items']['Items'] : array('data'=>array(), 'totalcount' => 0);
        /*if (count($itemList['data']) == 1 && $this->getAndAssignSearchOffset() == 0) {
            header('Location: /' . UrlGenerator::generateItemUrl($itemList['data'][0]['id']));
        }*/
        $count = $itemList['totalcount'];
        $maxCountPagination = $itemList['totalcount'];
        if (! empty($searchTypes)) {
            foreach ($searchTypes as $type) {
                if (
                    (! empty($foundAll['searchData'])) and
                    (! empty($foundAll['searchData']['Provider'])) and
                    ($type['Provider'] == $foundAll['searchData']['Provider']) and 
                    ($type['SearchMethod'] == $foundAll['searchData']['SearchMethod']) and 
                    ($itemList['totalcount'] >= $type['MaximumItemsCount'])
                   ) {
                    $maxCountPagination = $type['MaximumItemsCount'];
                    break;
                }
            }
        }

        // добавляем картинки для товаров, выбраннные админом
        $setsRepository = new SetsRepository($this->cms);
        $items = $setsRepository->addCutomImageForItems($itemList['data'], Session::getActiveLang());
        $items = $this->prepareQuantityRanges($items);

        $this->tpl->assign('itemlist', $items);
        if (isset($foundAll['Items']['CurrentImageUrl'])) {
            $this->tpl->assign('currentImageUrl', $foundAll['Items']['CurrentImageUrl']);
        }
        $this->tpl->assign('totalcount', $itemList['totalcount']);
        $this->tpl->assign('count', $count);
        $this->tpl->assign('maxCountPagination', $maxCountPagination);
        $this->tpl->assign('maximumPageCount', ! empty($foundAll['Items']) ? $foundAll['Items']['maximumpagecount'] : '');

        $this->tpl->assign('availableSorts', isset($foundAll['searchData']['AvailableSorts']) ?
            $foundAll['searchData']['AvailableSorts'] : array());
        
        $currentSort = isset($foundAll['Items']['CurrentSort']) ? $foundAll['Items']['CurrentSort'] : false;
        if($this->request->valueExists('sort_by')) {
            $currentSort = $this->request->getValue('sort_by');
        }
        $this->tpl->assign('currentSort', $currentSort);
    }

    private function prepareItemListMulti($foundAll)
    {
        $preData = $this->prePrepareItemListMulti($foundAll);
        $this->tpl->assign('itemlistMulti', $preData['itemList']);
        $lastSearchResult = end($preData['itemList']);
        $searchTypes = array_keys($preData['foundAll']);
        $lastSearchType = end($searchTypes);
        if($lastSearchResult && $lastSearchResult['totalcount'] >= 20 && count($lastSearchResult['Items']) >= 20){
            $this->tpl->assign('pagination', new Paginator($lastSearchResult['totalcount'], $page = (int)$this->request->get('page', 1), $this->getPerPageMulti($lastSearchType)));
        }
        if($this->isSearchMulti()){
            $this->tpl->assign('lastSearchProvider', isset($preData['lastSearchProvider']) ? $preData['lastSearchProvider'] : array());
        }
    }

    private function prePrepareItemListMulti($foundAll){
        $setsRepository = new SetsRepository($this->cms);
        $inFirst = array();
        $lastSearchProvider = array();
        $itemList = array();
        foreach ($foundAll as $key=>&$one) {
                $itemListTmp = array();
                $itemListTmp['Items'] = isset($one['Items']['Items']['data']) ? $one['Items']['Items']['data'] : array();
                $itemListTmp['totalcount'] = isset($one['Items']['Items']['totalcount']) ?  $one['Items']['Items']['totalcount'] : 0;
                $itemListTmp['Items'] = $setsRepository->addCutomImageForItems($itemListTmp['Items'], Session::getActiveLang());
                if ($itemListTmp['totalcount'] > 0)
                    $itemList[$key] = $itemListTmp;

                $lastSearchProvider['key'] = $key;
                $lastSearchProvider['totalCount'] = isset($one['Items']['Items']['totalcount']) ?  $one['Items']['Items']['totalcount'] : 0;
        }
        $firstSearchResults = current($itemList);

        if ($firstSearchResults && $inFirst ) {
            $firstItemId = isset($firstSearchResults['Items'][0]['id']) ? $firstSearchResults['Items'][0]['id'] : $inFirst[0]['Id'];
            /*if (count($itemList) == 1 && $firstSearchResults['totalcount'] == 1) {
                header('Location: /' . UrlGenerator::generateItemUrl($firstItemId));
                die();
            }*/
        }
        return array(
            'itemList'           => $itemList,
            'lastSearchProvider' => $lastSearchProvider,
            'foundAll'           => $foundAll
        );
    }

    private function prepareSearchProp($foundAll){
        $searchProperties = isset($foundAll['SearchProperties']) ? $foundAll['SearchProperties'] : array();
        $this->searchProp->setSearchProperties($searchProperties, ! empty($foundAll['searchData']) ? $foundAll['searchData'] : array());
        $this->searchProp->setBaseUrl($this->baseUrl);
        $this->searchProp->setClearUrl($this->clearUrl);
        $this->tpl->assign('SearchProp', $this->searchProp->Generate());
    }

     private function prepareSearchPropMulti($foundAll){
        $propIds = array();
        $searchProperties = array();
        foreach ($foundAll as $one) {
            if(!isset($one['SearchProperties']))
                continue;
            foreach((array)$one['SearchProperties'] as $prop){
                if(!in_array($prop['Id'], $propIds)){
                    $propIds[] = $prop['Id'];
                    $searchProperties[] = $prop;
                }
            }
        }
        $this->searchProp->setSearchProperties($searchProperties, array());
        $this->searchProp->setBaseUrl($this->baseUrl);
        $this->searchProp->setClearUrl($this->clearUrl);
        $this->tpl->assign('SearchProp', $this->searchProp->Generate());
     }

    /**
    * Функция исключает из массива подходящих категорий те,
    * которые указаны в ограничениях в админ. панели
    */
    private function checkFilteredCategory ($categories) {
        $filteredCategory = array();

        foreach ($categories as $category) {
            if (isset($category['isfiltered']) && $category['isfiltered'] == 'true') {
                continue;
            }
            $filteredCategory[] = $category;
        }

        return $filteredCategory;
    }

    private function prepareHintCats() {
        $this->otapilib->setErrorsAsExceptionsOff();

        if (!RequestWrapper::getValueSafe('search')) {
            $this->tpl->assign('hintcats', array());
        } else {
            $categoriesResult = $this->otapilib->FindHintCategoryInfoList(RequestWrapper::getValueSafe('search'));

            if (! is_array($categoriesResult)) {
                $categoriesResult = array();
            }

            $categoriesResult = $this->checkFilteredCategory($categoriesResult);

            if(in_array('Seo2', General::$enabledFeatures)){
                try {
                    $SeoCatsRepository = new SeoCategoryRepository(new CMS());
                    if(is_array($categoriesResult))
                    foreach($categoriesResult as &$c){
                        $c['alias'] = $SeoCatsRepository->getCategoryAlias($c['Id'], $c['Name']);
                    }
                } catch (DBException $e) {
                    Session::setError($e->getMessage(), 'DBError');
                }

            }

            $this->tpl->assign('hintcats', $categoriesResult);
        }
    }

    private function CheckSearch(){
        if ( ($this->request->get('search')!='') && ($this->request->get('cid')=='') && InstanceProvider::getObject()->isLimitItemsByCatalog() ) {
            return array('search' => false, 'reason' => 'category_not_selected');
        }

        if (($this->request->get('cid')=='') && ($this->request->get('search')=='') && ($this->request->get('brand')=='') && ($this->request->get('id')=='') && ($this->request->get('vid')=='') && ($this->request->get('imageId')=='')) {
            return array('search' => false, 'reason' => SCRIPT_NAME);
        } else {
            return array('search' => true, 'reason' => 'no');
        }
    }

    private function CheckMultiSearch($сategoryInfo){
        if (isset($сategoryInfo['SearchMethod'])) {
            $isCategorySearchMethod = $сategoryInfo['SearchMethod'] == '' ? false : true;
        } else {
            $isCategorySearchMethod = false;
        }
        if ($this->request->get('id')=='' && ($this->request->get('Provider')=='') && (General::getConfigValue('use_multi_search')) && (! $isCategorySearchMethod)) {
            return true;
        } else {
            return false;
        }
    }

    private function isChina(){
        $alowedIps = json_decode(General::getConfigValue('ip_access_to_search', json_encode(array())), true);
        if (in_array($_SERVER['SERVER_ADDR'], $alowedIps)) {
            return false;
        }
        if(!$this->cms->tableExists('ip2c')){
            chdir(CFG_APP_ROOT . '/lib/ip2country/');
            require_once 'import.php';
            chdir(CFG_APP_ROOT);
        }

        chdir(CFG_APP_ROOT . '/lib/ip2country/');
        require_once 'ip2country.php5.php';

        $ip2c=new ip2country();
        $ip2c->mysql_host=DB_HOST;
        $ip2c->db_user=DB_USER;
        $ip2c->db_pass=DB_PASS;
        $ip2c->db_name=DB_BASE;
        $ip2c->table_name='ip2c';
        chdir(CFG_APP_ROOT);

        return $ip2c->get_country_code() == 'CN';
    }

    private function getAndAssignSearchTypesInfo()
    {
        if ($this->fileMysqlMemoryCache->Exists('multi_search:' . $this->searchParametersHash)) {
            $cachedSearchTypesInfo = json_decode($this->fileMysqlMemoryCache->GetCacheEl('multi_search:' . $this->searchParametersHash), true);
            $preData = $this->prePrepareItemListMulti($cachedSearchTypesInfo);
            $this->tpl->assign('cachedSearchTypesInfo', $preData['itemList']);
        }
    }

    private function getCookiePerPage($searchType, $default)
    {
        return Cookie::get($searchType . '_perPageValue', $default);
    }

    private function setCookiePerPage($searchType, $value)
    {
        Cookie::set($searchType . '_perPageValue', $value, time()+86400*30);
    }

    private function getCountOfItemsByFilter()
    {                
        $categoryItemFilter = $this->searchParams();
        $categoryItemFilter = $this->searchParamsAddited($categoryItemFilter);
        
        $this->prepareSearchParametersHash($categoryItemFilter);
        $searchTypes = $this->getSearchMethods($categoryItemFilter);
        
        $categoryItemFilter = $this->searchParamsAddProvider($categoryItemFilter, $searchTypes);
        $newCategoryFilter = Plugins::invokeEvent('newCategoryFilterXML', array('xml' =>$categoryItemFilter));
        if ($newCategoryFilter)
            $categoryItemFilter = $newCategoryFilter;
        $searchXml = simplexml_load_string($categoryItemFilter);
        $this->prepareSearchParametersHash($categoryItemFilter);
        $searchTypes = $this->getSearchMethods($categoryItemFilter);
        $foundAll = $this->getCountOfSimpleItems($categoryItemFilter, $searchTypes);
        if (($foundAll) && (! empty($foundAll['Items']['Items']['totalcount']))) {
            print json_encode(array('Success'=>'Ok', 'Count' => $foundAll['Items']['Items']['totalcount']));
        } else {
            print json_encode(array('Success'=>'Ok', 'Count' => ' - '));
        }
        die;
    }

    private function getCountOfSimpleItems($categoryItemFilter, $SearchTypes){
        $this->otapilib->setErrorsAsExceptionsOn();
        $foundAll = false;
        $N_search = new SimpleXMLElement($categoryItemFilter);
        $N_search->OutputMode = 'TotalCount';

        $searchData = array();
        foreach ($SearchTypes as $type) {
            if (($type['Provider']==$N_search->Provider) && ($type['SearchMethod']==$N_search->SearchMethod)){
                $searchData = $type;
                break;
            }
        }
        try {
            $foundAll = $this->otapilib->BatchSearchItemsFrame(User::getObject()->getSid(), $N_search->asXML(), 0, 1, $this->generateSearchBlocks());
        }
        catch (ServiceException $e) {
            if ((string)$e->getErrorCode() != 'NotFound') {
                print json_encode(array('Success'=>'', 'message' => $e->getErrorMessage()));
                return false;
            }
        }
        catch(Exception $e){
            print json_encode(array('Success'=>'', 'message' => $e->getMessage()));
            return false;
        }
        return $foundAll;
    }
    
    private function prepareSearchParametersHash($categoryItemFilter){
        $searchXml = simplexml_load_string($categoryItemFilter);
        $searchParameters = array(
            'ItemTitle' => (string)$searchXml->ItemTitle,
            'CategoryId' => (string)$searchXml->CategoryId,
            'CategoryMode' => (string)$searchXml->CategoryMode,
            'OrderBy' => isset($searchXml->OrderBy) ? (string)$searchXml->OrderBy : false,
            'ImageFileId' => isset($searchXml->ImageFileId) ? (string)$searchXml->ImageFileId : false,
            'BrandPropertyValueId' => isset($searchXml->BrandPropertyValueId) ? (string)$searchXml->BrandPropertyValueId : false,
            'Configurators' => isset($searchXml->Configurators) ? (string)$searchXml->Configurators->asXML() : false
        );
        $this->searchParametersHash = md5(json_encode($searchParameters));
    }

    private function prepareQuantityRanges($items)
    {
        if (!empty($items)) {
            foreach ($items as &$item) {
                if (! empty($item['QuantityRanges'])) {
                    $quantityRanges = $item['QuantityRanges'];
                    $tmpRanges = array();

                    foreach ($quantityRanges as $idx => $range) {
                        $tmpRange = array();

                        if (count($quantityRanges) > 5 && 1 < $idx && $idx < count($quantityRanges) - 2) {
                            if ($idx == 2) {
                                $tmpRange['DisplayRange'] = '...';
                                $tmpRange['Price'] = '...';
                                $tmpRanges[] = $tmpRange;
                            }
                            continue;
                        }

                        $minQuantity = $range['MinQuantity'];
                        $tmpRange['MinRange'] = $minQuantity;

                        if (isset($quantityRanges[$idx + 1]) && !empty($quantityRanges[$idx + 1])) {
                            $maxQuantity = $quantityRanges[$idx + 1]['MinQuantity'] - 1;

                            if ($minQuantity != $maxQuantity) {
                                $tmpRange['DisplayRange'] = $minQuantity . ' - ' . $maxQuantity;
                            } else {
                                $tmpRange['DisplayRange'] = $minQuantity;
                            }
                        } else {
                            $tmpRange['DisplayRange'] = '&ge; ' . $minQuantity;
                        }
                        $tmpRange['DisplayRange'] .= ' ' . Lang::get('pcs');

                        $tmpRange['Price'] = TextHelper::formatPrice($range['Price']['ConvertedPriceWithoutSign']) . ' ' . $range['Price']['CurrencySign'];

                        $tmpRanges[] = $tmpRange;
                    }

                    $item['QuantityRanges'] = $tmpRanges;
                }
            }
        }

        return $items;
    }

}
