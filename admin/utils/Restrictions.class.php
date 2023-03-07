<?php

class Restrictions extends GeneralUtil
{
    protected $_template = 'goods';
    protected $_template_path = 'restrictions/';

    protected $restrictionsProvider;
    protected $setsProvider;
    protected $categoriesProvider;

    const CONTENT_TYPE_ITEM = 'Item';
    const CONTENT_TYPE_CATEGORY = 'Category';
    const CONTENT_TYPE_VENDOR = 'Vendor';
    const CONTENT_TYPE_STRING = 'SearchString';
    const CONTENT_TYPE_BRAND = 'Brand';

    public function __construct()
    {
        parent::__construct();

        $this->restrictionsProvider = new RestrictionsProvider();
        $this->categoriesProvider = new CategoriesProvider($this->cms, $this->getOtapilib());
        $this->setsProvider = new SetsProvider($this->cms, $this->getOtapilib());
    }

    function defaultAction($request)
    {
        try {
            $blackList = $this->getRestriction(self::CONTENT_TYPE_ITEM);
            $blackList = $this->prepareBlackListArray($blackList);
            $positions = $this->assignPagination($request, !empty($blackList) ? count($blackList) : 0);
            if (!empty($blackList)) {
                $this->restrictionsProvider->initGetItemInfoList(implode(";", array_slice($blackList, $positions['from'], ($positions['to'] - $positions['from']), true)));
                $this->restrictionsProvider->doRequests();
                $blackList = $this->restrictionsProvider->getItemInfoListResult()->GetOtapiItemInfoList()->GetContent();
            } else {
                $blackList = array();
            }
            $this->tpl->assign('blackList', $blackList);

        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);
        }
        print $this->fetchTemplate();
    }

    function categoriesAction($request)
    {
        try {
            $blackList = $this->getRestriction(self::CONTENT_TYPE_CATEGORY);
            $categories = $this->categoriesProvider->GetEditableCategorySubcategories(Session::get('sid'), 0, 'true');
            $i = 0;
            if (!is_array($categories)) {
                throw new ServiceException(__METHOD__, '', 'Could not load categories list', 1);
            }
            foreach ($categories as $k => &$category) {
                $category['i'] = $i;
                $i++;
            }
            $blackList = $this->prepareBlackListArray($blackList);
            $positions = $this->assignPagination($request, !empty($blackList) ? count($blackList) : 0);

        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);
        }
        $packBlackList = array();
        if (!empty($blackList)) {
            for ($i = $positions['from']; $i < $positions['to']; $i++) {
                try {
                    $packBlackList[$blackList[$i]] = $blackList[$i];
                    $this->restrictionsProvider->initGetCategoryInfo((string)$blackList[$i]);
                    $this->restrictionsProvider->doRequests();
                    $packBlackList[$blackList[$i]] = $this->restrictionsProvider->getCategoryInfoResult();
                } catch (ServiceException $e) {
                    Session::setError($e->getErrorMessage());
                }
            }
        }
        $this->tpl->assign('blackList', $packBlackList);
        $this->tpl->assign('categories', $categories);
        $this->_template = 'categories';
        print $this->fetchTemplate();
    }

    function sellersAction($request)
    {
        try {
            $blackList = $this->getRestriction(self::CONTENT_TYPE_VENDOR);
            $blackList = $this->prepareBlackListArray($blackList);
            $positions = $this->assignPagination($request, !empty($blackList) ? count($blackList) : 0);
        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);
        }
        $packBlackList = array();
        if (!empty($blackList)) {
            for ($i = $positions['from']; $i < $positions['to']; $i++) {
                try {
                    $packBlackList[$blackList[$i]] = $blackList[$i];
                    $packBlackList[$blackList[$i]] = $this->setsProvider->GetVendorInfo((string)$blackList[$i]);
                } catch (ServiceException $e) {
                }
            }
        }

        $this->_template = 'sellers';
        $this->tpl->assign('blackList', $packBlackList);
        print $this->fetchTemplate();
    }

    function searchesAction($request)
    {
        //TODO  - Сейчас не выводятся
        $this->_template = 'searches';
        try {
            $blackList = $this->getRestriction(self::CONTENT_TYPE_STRING);
            $blackList = $this->prepareBlackListArray($blackList);
            $this->assignPagination($request, !empty($blackList) ? count($blackList) : 0);
            $page = $this->getPageDisplayParams($request);
            if (count($blackList) > $page['limit']){
                $blackList = array_chunk($blackList,$page['limit']);
                $blackList = $blackList[(($page['number'] == 0) ? 1 : $page['number'])- 1];
            }
            $this->tpl->assign('blackList', $blackList);
        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);
        }
        print $this->fetchTemplate();
    }

    function brandsAction($request)
    {
        try {
            $blackList = $this->getRestriction(self::CONTENT_TYPE_BRAND);
            $blackList = $this->prepareBlackListArray($blackList);
            $positions = $this->assignPagination($request, !empty($blackList) ? count($blackList) : 0);
        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);
        }
        $packBlackList = array();
        if (!empty($blackList)) {
            for ($i = $positions['from']; $i < $positions['to']; $i++) {
                try {
                    $packBlackList[$blackList[$i]] = $blackList[$i];
                    $this->restrictionsProvider->initGetBrandInfo($blackList[$i]);
                    $this->restrictionsProvider->doRequests();
                    $packBlackList[$blackList[$i]] = $this->restrictionsProvider->getGetBrandInfoResult();
                } catch (ServiceException $e) {
                    Session::setError($e->getErrorMessage());
                }
            }
        }
        $this->_template = 'brands';
        $this->tpl->assign('blackList', $packBlackList);
        print $this->fetchTemplate();
    }

    function addRestrictionAction($request)
    {
        try {
            $xml = "<ArrayOfContentList><ContentList ContentType='" . $request->getValue('type') . "'>";
            $item = $this->prepateRestrictionItem($request->getValue('type'), $request->getValue('restrictionData'));
            $xml .= "<Content>" . $item . "</Content>";
            $xml .= "</ContentList></ArrayOfContentList>";
            
            $this->restrictionsProvider->AddBlackListContents($xml);
            $this->restrictionsProvider->doRequests();
            
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    function deleteRestrictionAction($request)
    {
        try {
            $deleteData = $request->getValue('restrictionData');
            $xml = "<ArrayOfContentList><ContentList ContentType='" . $request->getValue('type') . "'>";
            foreach ($deleteData as $item) {
                $xml .= "<Content>" . htmlspecialchars($item) . "</Content>";
            }
            $xml .= "</ContentList></ArrayOfContentList>";
            
            $result = false;
            $this->restrictionsProvider->DeleteBlackListContents($xml);
            $this->restrictionsProvider->doRequests();
            
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    /**
     * @param $type
     * @return OtapiContentList
     */
    private function getRestriction($type)
    {
        $this->restrictionsProvider->initGetBlackListContents();
        $this->restrictionsProvider->doRequests();
        /**
         * @return OtapiContentList[]
         */
        $packBlackList = new OtapiContentList('');
        $blackList = $this->restrictionsProvider->getBlackListContents()->GetResult()->GetContentList();
        foreach ($blackList as $list) {
            if ($list->GetContentTypeAttribute() == $type) {
                $packBlackList = $list;
                break;
            }
        }
        return $packBlackList;
    }

    private function assignPagination($request, $count)
    {
        $page = $this->getPageDisplayParams($request);
        $perPage = $page['limit'];
        $from = $page['offset'];
        $page = $page['number'];

        $this->tpl->assign('paginator', new Paginator($count, $page, $perPage));
        $this->tpl->assign('perPage', $perPage);
        $nextCount = $from + $perPage;
        return array(
            'from' => $from,
            'to' => $nextCount > $count ? $count : $nextCount
        );
    }

    /**
     * @param OtapiContentList $blackList
     * @return array
     */
    private function prepareBlackListArray($blackList)
    {
        $blackListArray = array();
        foreach ($blackList->GetContent() as $item) {
            if (!empty($item)) {
                $blackListArray[] = (string)$item;
            }
        }
        return $blackListArray;
    }

    private function prepateRestrictionItem($type, $item)
    {
        switch ($type) {
            case self::CONTENT_TYPE_ITEM:
                if (preg_match('/('.UrlGenerator::getProtocol().')/i', $item)) {
                    $url = parse_url($item);
                    if (!isset($url['query'])) {
                        throw new Exception(LangAdmin::get('Item_url_is_invalid'));
                    }
                    $params = array();
                    parse_str($url['query'], $params);
                    if (isset($params['id'])) {
                        return htmlspecialchars($params['id']);
                    }
                } else {
                    throw new Exception(LangAdmin::get('Item_url_is_invalid'));
                }
                break;
            case self::CONTENT_TYPE_CATEGORY:
                if (preg_match('/('.UrlGenerator::getProtocol().')/i', $item)) {
                    if (in_array('Seo2', General::$enabledFeatures)) {
                        $parts = explode("/", $item);
                        $parts = preg_replace("/(\?.*)$/", "", end($parts));
                        if ($parts === "search") {
                            $pattern = "/cid=([a-zA-Z0-9-]+)[&]?/";
                            $parts = preg_match($pattern, $item, $matches);
                            $cid = $matches[1];
                        } else {
                            $cid = $this->cms->getCategoryIdByAlias($parts);
                        }
                        if ($cid) {
                            return htmlspecialchars($cid);
                        } else {
                            throw new Exception(LangAdmin::get('Category_url_is_invalid'));
                        }
                    } else {
                        $url = parse_url($item);
                        $params = array();
                        if (isset($url['query'])) {
                            parse_str($url['query'], $params);
                            if (isset($params['cid'])) {
                                return htmlspecialchars($params['cid']);
                            } else {
                                throw new Exception(LangAdmin::get('Category_url_is_invalid'));
                            }
                        } else {
                            throw new Exception(LangAdmin::get('Category_url_is_invalid'));
                        }
                    }
                } else {
                    if (!empty($item)) {
                        return $item;
                    }
                    throw new Exception(LangAdmin::get('Category_url_is_invalid'));
                }
                break;
            case self::CONTENT_TYPE_VENDOR:
                if (preg_match('/('.UrlGenerator::getProtocol().')/i', $item)) {
                    $url = parse_url($item);

                    if (!isset($url['query'])) {
                        throw new Exception(LangAdmin::get('Vendor_url_is_invalid'));
                    }

                    $params = array();
                    parse_str($url['query'], $params);
                    $path = rtrim($url['path'], '/');

                    if ($path == '/item') {
                        if (isset($params['vendorId'])) {
                            return htmlspecialchars($params['vendorId']);
                        } else {
                            throw new Exception(LangAdmin::get('Vendor_url_is_invalid'));
                        }
                    } elseif ($path == '/vendor') {
                        if (isset($params['id'])) {
                            return htmlspecialchars($params['id']);
                        } else {
                            throw new Exception(LangAdmin::get('Vendor_url_is_invalid'));
                        }
                    }
                } else {
                    return htmlspecialchars($item);
                }
                break;
            case self::CONTENT_TYPE_STRING:
                if (preg_match('/('.UrlGenerator::getProtocol().')/i', $item)) {
                    $url = parse_url($item);
                    $params = array();
                    parse_str($url['query'], $params);
                    if (isset($params['search'])) {
                        return htmlspecialchars($params['search']);
                    } else {
                        throw new Exception(LangAdmin::get('Search_url_is_invalid'));
                    }
                } else {
                    return htmlspecialchars($item);
                }
                break;
            case self::CONTENT_TYPE_BRAND:
                if (preg_match('/('.UrlGenerator::getProtocol().')/i', $item)) {
                    $url = parse_url($item);
                    if (!isset($url['query'])) {
                        throw new Exception(LangAdmin::get('Brand_url_is_invalid'));
                    }
                    $params = array();
                    parse_str($url['query'], $params);
                    if (isset($params['brand'])) {
                        return htmlspecialchars($params['brand']);
                    }
                } else {
                    throw new Exception(LangAdmin::get('Brand_url_is_invalid'));
                }
                break;
        }
    }
}
