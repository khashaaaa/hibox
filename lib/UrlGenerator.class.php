<?php

class UrlGenerator
{
    /**
     * @var Array
     */
    private static $registry = array();

    //UrlGenerator::getHomeUrl()
    //"//"+window.location.hostname
    public static function getHomeUrl()
    {
        $url = self::getProtocol() .'://' . IDN::decodeIDN($_SERVER['SERVER_NAME']);
        return $url;
    }
    
    public static function generateSubcategoryUrl($params, $isAbsolute = false)
    {
        $url = '';
        $addParams = array();
        $instanceProvider = InstanceProvider::getObject();
        if(@$params['isvirtual'] == 'true') $addParams['virt'] = 'virt';
        if(@$params['clear']) $addParams['clear'] = 'clear';
        if(@$params['root']) $addParams['root'] = 'root';
        if(@$params['brand']) $addParams['brand'] = 'brand='.@$params['brand'];
        if(@$params['ProviderType'] == 'Warehouse'){
            $addParams['Provider'] = 'Provider=' . $instanceProvider->GetAliasByProviderName(Session::getActiveLang(), @$params['ProviderType']);
            $addParams['SearchMethod'] = 'SearchMethod=Default';
        }
        if(@$params['Provider']) {
            $addParams['Provider'] = 'Provider='.@$params['Provider'];
            $addParams['SearchMethod'] = 'SearchMethod='.@$params['SearchMethod'];
        }
        if (in_array('Seo2', General::$enabledFeatures)) {
            if (! isset($params['alias']) && !empty($params['Name']) && (isset($params['Id']) && (!empty($params['Id']) || $params['Id'] === '0'))) {
                $seoCatModel = new SeoCategoryRepository(new CMS());
                $params['alias'] = $seoCatModel->getCategoryAlias($params['Id'], $params['Name']);
            }
            $alias = htmlspecialchars(rawurlencode($params['alias']), ENT_QUOTES);
            $url = '/subcategory/' . $alias;
            if (count($addParams)) {
                $url .= '?' . implode('&', $addParams);
            }
        } else {
            $url = '/?p=subcategory&cid=' . (isset($params['Id']) ? $params['Id'] : $params['id']);
            if (count($addParams)) {
                $url .= '&' . implode('&', $addParams);
            }
        }
        if ($isAbsolute) {
            $url = self::getHomeUrl() . $url;
        }
        return $url;
    }

    public static function generateCategoryUrl($params, $isAbsolute = false){
        $url = '';
        $addParams = array();

        if (@$params['isvirtual'] == 'true') $addParams['virt'] = 'virt';
        if (@$params['clear']) $addParams['clear'] = 'clear';

        if (in_array('Seo2', General::$enabledFeatures)) {
            if (! isset($params['alias']) && !empty($params['Id'])) {
                $categoryName = !empty($params['Name'])?$params['Name']:'';
                $seoCatModel = new SeoCategoryRepository(new CMS());
                $params['alias'] = $seoCatModel->getCategoryAlias($params['Id'], $categoryName);
            }

            $params['alias'] = htmlspecialchars(rawurlencode($params['alias']), ENT_QUOTES);
            $url = '/category/' . $params['alias'];
            if(count($addParams)) {
                $url .= '/?'.implode('&', $addParams);
            }
        } else {
            $url = '/?p=category&cid='.$params['Id'];
            if (count($addParams)) {
                $url .= '&'.implode('&', $addParams);
            }
        }

        if ($isAbsolute) {
            $url = self::getHomeUrl() . $url;
        }

        return $url;
    }



    public static function generateFullSearchUrl($url,$cid,$tmall = false,$discounts = false){
        $url = str_replace('cid='.RequestWrapper::getValueSafe('cid'), '', $url);

        if($tmall) $addParams['tmall'] = 'tmall=true';
        if($discounts) $addParams['discounts'] = 'discounts=true';

        $url.= '&cid='.$cid;
        if(count($addParams))
          $url.= '&'.implode('&', $addParams);


        return $url;
    }

    public static function generateAllcatsUrl($params){
        $url = '';
        if(in_array('Seo2', General::$enabledFeatures)){
            $url .= '/allcats';
        }
        else{
            $url = '/?p=allcats';
        }

        return $url;
    }
    public static function generateReviewsUrl($params = array()){
        $url = '';
        if (in_array('Seo2', General::$enabledFeatures)) {
            $url .= '/reviews';
            if(count($params))
                $url .= '?'.http_build_query($params);
        } else {
            $url = '/?p=reviews';
            if(count($params))
                $url .= '&'.http_build_query($params);
        }

        return $url;
    }

    public static function generateBrandsUrl($params = array()){
        $url = '';
        if(in_array('Seo2', General::$enabledFeatures)){
            $url .= '/brands';
        }
        else{
            $url = '/?p=brands';
        }

        return $url;
    }

    public static function generateSearchBrandsUrl($brandId, $isAbsolute = false){
        $url = '';
        if(in_array('Seo2', General::$enabledFeatures)){
            $url .= '/search?search=&brand=' . $brandId;
        }
        else{
            $url = '/?p=search&search=&brand=' . $brandId;
        }
        if ($isAbsolute) {
            $url = self::getHomeUrl() . $url;
        }
        return $url;
    }

    public static function generateBrandUrl($id){
        $url = '';
        if(General::IsFeatureEnabled('Seo2')){
            $url .= '/search?brand='.$id;
        }
        else{
            $url = '/?p=search&brand='.$id;
        }

        return $url;
    }

    public static function generateContentUrl($p, $isAbsolute = false, $id = false) {
        $url = '';
        $p = rawurlencode($p);
        if(in_array('Seo2', General::$enabledFeatures)){
            $url .= '/' . $p;
        }
        else{
            $url = '/?p='.$p;
        }
        if ($id) {
            if(in_array('Seo2', General::$enabledFeatures)){
                $url .= '?pid=' . $id;
            } else {
                $url .= '&pid=' . $id;
            }
        }
        if ($isAbsolute) {
            $url = self::getHomeUrl() . $url;
        }
        return $url;
    }

    public static function generateNewsUrl($newsId, $isAbsolute = false){
        $url = '';
        if(in_array('Seo2', General::$enabledFeatures)){
            $url = '/news?id=' . $newsId;
        } else {
            $url = '/?p=news&id=' . $newsId;
        }
        if ($isAbsolute) {
            $url = self::getHomeUrl() . $url;
        }
        return $url;
    }

    public static function generateDigestUrl($p,$p2){
        $url = '';
        if(in_array('Seo2', General::$enabledFeatures)){
            $url .= '/' . $p."?cat=".$p2;
        }
        else{
            $url = '/?p='.$p.'&cat='.$p2;
        }

        return $url;
    }
    public static function generatPostUrl($p, $p2, $alias){
        $url = '';
        $alias = rawurlencode($alias);
        if (in_array('Seo2', General::$enabledFeatures)) {
            if (! empty($alias)) {
                $url .=  '/' . $p."/" . $alias;
            } else {
                $url .= '/' . $p . "?id=" . $p2;
            }
        } else {
            $url = '/?p='.$p.'&id='.$p2;
        }

        return $url;
    }

    public static function generateSearchUrl($searchString = false, $isAbsolute = false){
        $url = '';
        if(in_array('Seo2', General::$enabledFeatures)){
            $url .= '/search';
        } else{
            $url .= '/?p=search';
        }
        if ($searchString) {
            if(in_array('Seo2', General::$enabledFeatures)){
                $url .= '/?search=' . $searchString;
            } else{
                $url .= '&search=' . $searchString;
            }
        }
        if ($isAbsolute) {
            $url = self::getHomeUrl() . $url;
        }
        return $url;
    }

    /**
     * @param int $id
     * @param array $options
     * @return string
     */
    public static function generateItemUrl($id, array $options = array())
    {
        // Для совместимости
        if (! is_array($options)) {
            $options = array('isAbsolute' => $options);
        }

        $url = '';

        if (in_array('Seo2', General::$enabledFeatures)) {
            $url .= '/item?id=' . rawurlencode($id);
        } else {
            $url .= '/?p=item&id=' . rawurlencode($id);
        }

        if (isset($options['vendorId'])) {
            $url .= '&vendorId=' . rawurlencode($options['vendorId']);
        }

        if (isset($options['cid'])) {
            $url .= '&cid=' . rawurlencode($options['cid']);
        }

        if (isset($options['ConfigId']) && $options['ConfigId']) {
            $url .= '#' . rawurlencode($options['ConfigId']);
        }

        if (isset($options['isAbsolute']) && $options['isAbsolute']) {
            $url = self::getHomeUrl() . $url;
        }

        return $url;
    }

    public static function generateWarehouseItemUrl($id)
    {
        $url = '';
        if (in_array('Seo2', General::$enabledFeatures)) {
            $url .= '/item?id=wh-'.$id;
        }
        else{
            $url = '/?p=item&id=wh-'.$id;
        }

        return $url;
    }

    public static function generatePristroyUrl()
    {
        $url = '';
        if(in_array('Seo2', General::$enabledFeatures)){
            $url .= '/pristroy';
        }
        else{
            $url = '/?p=pristroy';
        }
    
        return $url;
    }
    
    public static function generatePristroyItemUrl($id)
    {
        $url = '';
        if(in_array('Seo2', General::$enabledFeatures)){
            $url .= '/pristroy/item?id='.$id;
        }
        else{
            $url = '/?p=pristroy&id='.$id.'&action=item';
        }

        return $url;
    }

    public static function generatePristroySellerUrl($id)
    {
        $url = '';
        if(in_array('Seo2', General::$enabledFeatures)){
            $url .= '/pristroy/seller?id='.$id;
        }
        else{
            $url = '/?p=pristroy&id='.$id.'&action=seller';
        }

        return $url;
    }

    public static function generateVendorUrl($id, $isAbsolute = false, $cid = null, $alias = null) {
        if (!empty($alias)) {
            $url = CMS::IsFeatureEnabled('Seo2') ? '/vendor/'.$alias : '/?p=vendor/'.$alias;
        } else {
            $url = CMS::IsFeatureEnabled('Seo2') ? '/vendor?id='.$id : '/?p=vendor&id='.$id;
        }
        if ($cid) {
            $url .= '&cid=' . $cid;
        }
        if ($isAbsolute) {
            $url = self::getHomeUrl() . $url;
        }
        return $url;
    }

    public static function generateVendorsUrl(){
        return CMS::IsFeatureEnabled('Seo2') ? '/all_vendors' : '/?p=all_vendors';
    }

    public static function generateFavouritesUrl($params){
        switch($params['action']){
            case 'move_to_basket':
                $url = CMS::IsFeatureEnabled('Seo2') ? '/move_to_basket?id=' : '/?p=move_to_basket&id=';
                return $url . $params['id'];
                break;
        }
        return '';
    }

    public static function generateBasketUrl($params){
        switch($params['action']){
            case 'move_to_favourites':
                $url = CMS::IsFeatureEnabled('Seo2') ? '/move_to_favourites?id=' : '/?p=move_to_favourites&id=';
                return $url . $params['id'];
                break;
        }
        return '';
    }

    public static function generateOrderDetailsUrl($orderId, $params = array(),$fullPath = false){
        if ($fullPath) {
            $url = self::getHomeUrl();
        } else {
            $url = "";
        }
        $url.= CMS::IsFeatureEnabled('Seo2') ? '/orderdetails?' : '/?p=orderdetails';
        $url.= '&orderid=' . $orderId;
        if($params)
            $url.= '&' . http_build_query($params);
        return $url;
    }

    public static function generatePrivateOfficeUrl($params = array(), $isAbsolute = false){
        $url = CMS::IsFeatureEnabled('Seo2') ? '/privateoffice' : '/?p=privateoffice';
        if($params)
            $url .= '&' . http_build_query($params);

        if($isAbsolute) {
            $url = self::getHomeUrl() . $url;
        }
        return $url;
    }
    
    public static function generateSupportUrl($params, $isAbsolute = false)
    {
        if (isset($params['mode']) && $params['mode'] == 'chat') {
            if (isset($params['id']) && strpos($params['id'], 'Ticket') === false) {
                $params['id'] = 'Ticket-' . $params['id'];
            }
        }
        if (in_array('Seo2', General::$enabledFeatures)) {
            $url = '/support' . (!empty($params) ? '?' . http_build_query($params) : '');
        } else {
            $url = '/?p=support' . (!empty($params) ? '&' . http_build_query($params) : '');
        }
        if($isAbsolute) {
            $url = self::getHomeUrl() . $url;
        }
        return $url;
    }

    public static function generateFullCatUrl($type, $params, $tmall = false, $discounts = false){
        $addParams = array();
        if (@$params['isvirtual'] == 'true') $addParams['virt'] = 'virt';
        if (@$params['clear']) $addParams['clear'] = 'clear';
        if (@$params['root']) $addParams['root'] = 'root';
        if (@$params['brand']) $addParams['brand'] = 'brand='.@$params['brand'];

        if (@$params['Provider']) $addParams['Provider'] = 'Provider='.@$params['Provider'];
        if (@$params['SearchMethod']) $addParams['SearchMethod'] = 'SearchMethod='.@$params['SearchMethod'];

        if ($tmall) $addParams['tmall'] = 'tmall=true';
        if ($discounts) $addParams['discounts'] = 'discounts=true';

        if (in_array('Seo2', General::$enabledFeatures)) {
            $alias = htmlspecialchars(rawurlencode($params['alias']), ENT_QUOTES);
            $url = '/' . $type.'/'.$alias;
            if (count($addParams)) {
                $url .= '?'.implode('&', $addParams);
            }
        } else {
            $url = '/?p='.$type.'&cid='.$params['id'];
            if (count($addParams)) {
                $url .= '&'.implode('&', $addParams);
            }
        }

        return $url;
    }

    public static function redirectToSeoUrl($params) {
        switch ($params['p']) {
            case 'subcategory':
                $params['Id'] = $params['id'] = $params['cid'];
                $params['Name'] = $params['cid'];
                $url = self::generateSubcategoryUrl($params, true);
                break;
            case 'category':
                $params['Id'] = $params['id'] = $params['cid'];
                $params['Name'] = $params['cid'];
                $url = self::generateCategoryUrl($params, true);
                break;
            default:
                $url = '/' . $params['p'];
                unset($params['p']);
                if (count($params)) {
                    $url .= '?' . http_build_query($params);
                }
                break;
        }
        return $url;
    }

    public static function generateSearchUrlByParams(array $params = array(), array $options = array())
    {
        $seo = (in_array('Seo2', General::$enabledFeatures));
        $addParams = array();

        // особенность: для категорий SearchMethod оставляем только если текущий провайдер равен провайдеру категории
        if (! empty($params['Provider']) && ! empty($params['OtapiCategory'])) {
            // для объекта OtapiSearchCategoryInfo проверку делать не надо, т.к. оно гарантированно от того же провайдера
            $category = $params['OtapiCategory'];
            if (
                $category instanceof OtapiCategory &&
                $category->GetProviderType() != InstanceProvider::getObject()->GetProviderNameByAlias(Session::getActiveLang(), $params['Provider'])
            ) {
                unset($params['SearchMethod']);
                unset($params['vid']);
                unset($params['brand']);
            }
        }
        // особенность: для vid, brand, cid подставлять Provider в url необходимо только если параметр задан в GET
        // и если он пришел в паре с SearchMethod
        if (! empty($params['vid']) || ! empty($params['brand']) || ! empty($params['cid'])) {
            if (empty($params['SearchMethod'])) {
                unset($params['Provider']);
            }
        }

        // в зависимости от пришедших параметров выбираем генерируемый url отдельный или общий с алиасом search
        if (! empty($params['module'])) {
            $url = self::genSearchModuleUrl($params['module'], $params, $options);
            if ($url !== false) return $url;
        } elseif (! empty($params['vid'])) {
            $url = self::genVendorUrl($params['vid'], $params, $options);
            if ($url !== false) return $url;
        } elseif (! empty($params['brand'])) {
            $url = self::genBrandUrl($params['brand'], $params, $options);
            if ($url !== false) return $url;
        } elseif (! empty($params['cid'])) {
            $url = self::genCategoryUrl($params['cid'], $params, $options);
            if ($url !== false) return $url;
        }

        $url = '';
        if ($seo) {
            $url .= '/search';
        } else {
            $addParams['p'] = 'search';
        }

        if (! empty($params['Provider'])) $addParams['Provider'] = $params['Provider'];
        if (! empty($params['SearchMethod'])) $addParams['SearchMethod'] = $params['SearchMethod'];
        if (! empty($params['vid'])) $addParams['vid'] = $params['vid'];
        if (! empty($params['brand'])) $addParams['brand'] = $params['brand'];
        if (! empty($params['cid'])) $addParams['cid'] = $params['cid'];
        if (! empty($params['search'])) $addParams['search'] = $params['search'];
        if (! empty($params['imageId'])) $addParams['imageId'] = $params['imageId'];

        // добавляем гет параметры поиска
        if (! $seo) $url .= '/';
        if (! empty($addParams)) $url .= '?';
        $url .= http_build_query($addParams);

        if (isset($options['isAbsolute']) && $options['isAbsolute']) {
            $url = self::getHomeUrl() . $url;
        }

        return $url;
    }

    private static function genSearchModuleUrl($module, array $params = array(), array $options = array())
    {
        $seo = (General::IsFeatureEnabled('Seo2'));
        $addParams = array();

        $url = '';
        if ($seo) {
            $url .= '/' . strtolower($module);
        } else {
            $addParams['p'] = strtolower($module);
        }

        if (! empty($params['Provider'])) $addParams['Provider'] = $params['Provider'];
        if (! empty($params['SearchMethod'])) $addParams['SearchMethod'] = $params['SearchMethod'];
        if (! empty($params['vid'])) $addParams['vid'] = $params['vid'];
        if (! empty($params['brand'])) $addParams['brand'] = $params['brand'];
        if (! empty($params['cid'])) $addParams['cid'] = $params['cid'];
        if (! empty($params['search'])) $addParams['search'] = $params['search'];
        if (! empty($params['imageId'])) $addParams['imageId'] = $params['imageId'];

        // добавляем гет параметры поиска
        if (! $seo) $url .= '/';
        if (! empty($addParams)) $url .= '?';
        $url .= http_build_query($addParams);

        if (isset($options['isAbsolute']) && $options['isAbsolute']) {
            $url = self::getHomeUrl() . $url;
        }

        return $url;
    }

    private static function genVendorUrl($vendorId, array $params = array(), array $options = array())
    {
        $seo = (General::IsFeatureEnabled('Seo2'));
        $addParams = array();

        $url = '';

        if ($seo) {
            $vendorRepository = new VendorRepository(General::getCms());
            $vendorInfo = $vendorRepository->GetVendorInfo($vendorId);
            $alias = !empty($vendorInfo[0]['alias']) ? $vendorInfo[0]['alias'] : null;
            if (! $alias) {
                return false;
            }
            $url .= '/vendor/' . $alias;
        } else {
            $addParams['p'] = 'vendor';
            $addParams['id'] = $vendorId;
        }

        if (! empty($params['Provider'])) $addParams['Provider'] = $params['Provider'];
        if (! empty($params['brand'])) $addParams['brand'] = $params['brand'];
        if (! empty($params['cid'])) $addParams['cid'] = $params['cid'];
        if (! empty($params['search'])) $addParams['search'] = $params['search'];
        if (! empty($params['imageId'])) $addParams['imageId'] = $params['imageId'];

        // добавляем гет параметры поиска
        if (! empty($addParams)) $url .= '/?';
        $url .= http_build_query($addParams);

        if (isset($options['isAbsolute']) && $options['isAbsolute']) {
            $url = self::getHomeUrl() . $url;
        }

        return $url;
    }

    private static function genBrandUrl($brandId, array $params = array(), array $options = array())
    {
        $seo = (General::IsFeatureEnabled('Seo2'));
        $addParams = array();

        $url = '';

        // TODO: проверяем наличие бренда в базе
        if (! false) {
            return false;
        }

        if ($seo) {
            $url .= '/brand/' . $brandId;
        } else {
            $addParams['p'] = 'brand';
            $addParams['brand'] = $brandId;
        }

        if (! empty($params['Provider'])) $addParams['Provider'] = $params['Provider'];
        if (! empty($params['cid'])) $addParams['cid'] = $params['cid'];
        if (! empty($params['search'])) $addParams['search'] = $params['search'];
        if (! empty($params['imageId'])) $addParams['imageId'] = $params['imageId'];

        // добавляем гет параметры поиска
        if (! empty($addParams)) $url .= '/?';
        $url .= http_build_query($addParams);

        if (isset($options['isAbsolute']) && $options['isAbsolute']) {
            $url = self::getHomeUrl() . $url;
        }

        return $url;
    }

    private static function genCategoryUrl($categoryId, array $params = array(), array $options = array())
    {
        $seo = (General::IsFeatureEnabled('Seo2'));
        $addParams = array();

        $url = '';

        if ($seo) {
            $alias = self::getCategoryAlias($categoryId);
            if (! $alias) {
                return false;
            }
            $url .= '/subcategory/' . $alias;
        } else {
            $addParams['p'] = 'subcategory';
            $addParams['cid'] = $categoryId;
        }

        if (! empty($params['Provider'])) $addParams['Provider'] = $params['Provider'];
        if (! empty($params['SearchMethod'])) $addParams['SearchMethod'] = $params['SearchMethod'];
        if (! empty($params['search'])) $addParams['search'] = $params['search'];
        if (! empty($params['imageId'])) $addParams['imageId'] = $params['imageId'];

        // добавляем гет параметры поиска
        if (! $seo) $url .= '/';
        if (! empty($addParams)) $url .= '?';
        $url .= http_build_query($addParams);

        if (isset($options['isAbsolute']) && $options['isAbsolute']) {
            $url = self::getHomeUrl() . $url;
        }

        return $url;
    }

    private static function getCategoryAlias($categoryId)
    {
        // если категория есть в регистре
        if (! empty(self::$registry['categories'][$categoryId])) {
            // если алиас ранее не определен
            if (! isset(self::$registry['categories'][$categoryId]['alias'])) {
                // запускаем прогрев всех алиасов для категорий из регистра
                self::warmupCategoryAlias();
            }

            if (self::$registry['categories'][$categoryId]['alias']) {
                return rawurlencode(self::$registry['categories'][$categoryId]['alias']);
            }

            return false;
        }

        // проверяем наличие категории в базе
        $seoCategoryRepository = new SeoCategoryRepository(General::getCms());
        $alias = $seoCategoryRepository->getAliasById($categoryId);

        return ($alias) ? rawurlencode($alias) : false;
    }

    // функция для добавления в память категорий по которым надо сгенерить ссылки
    public static function addCategoriesForWarmup($categories)
    {
        foreach ($categories as $cat) {
            if (is_array($cat)) {
                $id = $cat['Id'];
                $name = $cat['Name'];
                $createAlias = $cat['IsInternal'] == 'false';
            } else {
                $id = $cat->GetId();
                $name = $cat->GetName();
                $createAlias = $cat->IsInternal();
            }

            if (empty(self::$registry['categories'][$id])) {
                self::$registry['categories'][$id]['name'] = $name;
                self::$registry['categories'][$id]['createAlias'] = $createAlias;
            }
        }
    }

    // получаем все алиасы для категорий из памяти
    public static function warmupCategoryAlias()
    {
        $ids = array();
        foreach (self::$registry['categories'] as $id => $data) {
            if (isset($data['alias'])) {
                continue;
            }
            $ids[] = $id;
        }
        $seoCategoryRepository = new SeoCategoryRepository(General::getCms());
        $aliases = $seoCategoryRepository->getCategoryAliases($ids);

        foreach ($ids as $id) {
            $alias = false;
            if (! empty($aliases[$id]['alias'])) {
                $alias = $aliases[$id]['alias'];
            }

            // если алиаса нет в базе - создаем на основе названия
            if (empty($alias) && self::$registry['categories'][$id]['createAlias']) {
                $seoCategoryRepository = new SeoCategoryRepository(General::getCms());
                $alias = $seoCategoryRepository->getCategoryAlias($id, self::$registry['categories'][$id]['name']);
            }

            self::$registry['categories'][$id]['alias'] = $alias;
        }
    }

    /**
     * @param string $uri
     * @param array $options
     * @return string
     */
    public static function getUrl($uri, array $options = array())
    {
        $url = '';

        if (CMS::IsFeatureEnabled('Seo2')) {
            $url .= '/' . $uri;
        } else {
            $url .= '/?q=' . $uri;
        }

        if (isset($options['includeGet']) && $options['includeGet']) {
            $getParams = (! empty($options['getParams'])) ? $options['getParams'] : $_GET;
            unset($getParams['q']);
            unset($getParams['p']);
            unset($getParams['action']);
            $getParams = http_build_query($getParams);

            if (CMS::IsFeatureEnabled('Seo2')) {
                if (! empty($getParams)) {
                    $url .= '?' . $getParams;
                }
            } else {
                $url .= '&' . $getParams;
            }
        }

        if (isset($options['includeQuestionMark']) && $options['includeQuestionMark']) {
            if (strpos($url, '?') === false) {
                $url .= '?';
            }
        }

        if (isset($options['isAbsolute']) && $options['isAbsolute']) {
            $url = self::getHomeUrl() . $url;
        }

        return $url;
    }

    /**
     * @param string $route
     * @param array $params
     * @param bool $isAbsolute
     * @return string
     */
    public static function toRoute($route, array $params = [], $isAbsolute = false)
    {
        $ruleDeclarations = Router::getInstance()->getRulesDeclarations();

        if (array_key_exists($route, $ruleDeclarations)) {
            $route = $ruleDeclarations[$route];
        }
        if (OTBase::isTest()) {
            $params['debug'] = 1;
        }
        $url = Router::getInstance()->createUrl($route, $params);

        if ($isAbsolute) {
            $url = self::getHomeUrl() . $url;
        }
        return $url;
    }

    /**
     * @return string
     */
    public static function getProtocol()
    {
        return General::getConfigValue('use_https', false) ? 'https' : 'http';
    }
}
