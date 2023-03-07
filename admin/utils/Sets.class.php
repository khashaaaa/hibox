<?php

OTBase::import('system.uploader.php.UploadHandler');
OTBase::import('system.lib.Validation.*');
OTBase::import('system.lib.Validation.Rules.*');


class Sets extends GeneralUtil
{
    protected $_template = 'brands';
    protected $_template_path = 'sets/';
    protected $setsProvider;
    protected $cacher;
    protected $categoriesProvider;
    protected $categoriesNewProvider;
    protected $warehouseProvider;
    public function __construct()
    {
        parent::__construct();
        $this->setsProvider = new SetsProvider($this->cms, $this->getOtapilib());
        $this->cacher = new FileAndMysqlMemoryCache($this->cms);
        $this->categoriesProvider = new CategoriesProvider($this->cms, $this->getOtapilib());
        $this->categoriesNewProvider = new CategoriesNewProvider();
        $this->warehouseProvider = new WarehouseProvider($this->getOtapilib());
    }

    public function defaultAction($request)
    {
        try {
            $type = 'Best';
            $contentType = 'Brand';
            $id = $this->getSetsConfig($type, $request);
            $language = $this->getActiveLang($request);
            $brandsList = $this->setsProvider->GetBrandRatingList('Best', 100, 0, $language);
            $brands = array();
            foreach ($brandsList as $i => $brand) {
                $brands[$brand['id']] = $brand;
            }

            $allBrands = $this->setsProvider->GetBrandInfoList();

            $this->tpl->assign('id', $id);
            $this->tpl->assign('type', $type);
            $this->tpl->assign('contentType', $contentType);
            $this->tpl->assign('brands', $brands);
            $this->tpl->assign('allBrands', $allBrands);
            $this->tpl->assign('languages', $this->languagesProvider->GetActiveLanguages());
            $this->tpl->assign('currentLang', $this->getActiveLang($request));
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }

        print $this->fetchTemplate();
    }

    public function clearSetAction($request)
    {
        $sid = Session::get('sid');
        $type = $request->getValue('type');
        $contentType = $request->getValue('contentType');
        $cid = $request->getValue('cid', 0);

        try {
            $this->setsProvider->RemoveAllElementsRatingList($sid, $type, $contentType, $cid);
            SetsUpdater::clearCachePart($this->getActiveLang($request), $contentType, $type, $cid);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse();
    }
    // save order of items by ids and types
    private function saveOrder($ids, $contentType, $type = 'Best', $cid = 0)
    {
        $sid = Session::get('sid');
        $result = $this->setsProvider->RemoveAllElementsRatingList($sid, $type, $contentType, $cid);
        if ($result) {
            $result = $this->setsProvider->AddElementsSetToRatingList($sid, $type, $contentType, $cid, $ids);
        }
    }

    public function saveItemsOrderAction($request)
    {
        try {
            $contentType = $request->getValue('contentType', 'Item'); //Item, Vendor, Category, Brand, SearchString
            $type = $request->getValue('type', 'Best'); //Best, Last, Popular etc.
            $ids = $request->getValue('ids');
            $cid = $request->getValue('cid', 0);
            $this->saveOrder($ids, $contentType, $type, $cid);

            SetsUpdater::clearCachePart($this->getActiveLang($request), $contentType, $type, $cid);
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    public function sellersAction($request)
    {
        $this->_template = 'sellers';

        $type = 'Best';
        $contentType = 'Vendor';
        $id = $this->getSetsConfig($type, $request);

        try {
            $language = $this->getActiveLang($request);
            $params = '<RatingListVendorSearchParameters><ItemRatingType>' . $type . '</ItemRatingType></RatingListVendorSearchParameters>';
            $sellers = $this->setsProvider->SearchRatingListVendors($params, 0, 50);
            $this->tpl->assign('id', $id);
            $this->tpl->assign('type', $type);
            $this->tpl->assign('contentType', $contentType);
            $this->tpl->assign('sellers', $sellers['Content']);
            $this->tpl->assign('totalCount', $sellers['TotalCount']);
            $this->tpl->assign('languages', $this->languagesProvider->GetActiveLanguages());
            $this->tpl->assign('currentLang', $language);
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }

        $pageUrl = new AdminUrlWrapper();
        $this->tpl->assign('pageUrl', $pageUrl);

        $sid = Session::get('sid');
        print $this->fetchTemplate();
    }

    public function moreSellersAction($request)
    {
        $this->_template = 'moresellers';

        $type = 'Best';
        $id = $this->getSetsConfig($type, $request);
        $offset = $request->getValue('offset', 51);

        try {
            $language = $this->getActiveLang($request);
            $params = '<RatingListVendorSearchParameters><ItemRatingType>' . $type . '</ItemRatingType></RatingListVendorSearchParameters>';
            $sellers = $this->setsProvider->SearchRatingListVendors($params, $offset, 50);
            foreach ($sellers['Content'] as $i => &$seller) {
                $seller['DisplayData'] = $this->setsProvider->getSetSellerInfo($seller['id'], $language);
            }
            $this->tpl->assign('id', $id);
            $this->tpl->assign('type', $type);
            $this->tpl->assign('sellers', $sellers['Content']);
            $this->tpl->assign('totalCount', $sellers['TotalCount']);
            $this->tpl->assign('languages', $this->languagesProvider->GetActiveLanguages());
            $this->tpl->assign('currentLang', $language);

            $result = $this->fetchTemplateWithoutHeaderAndFooter(false);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array('html' => $result));
    }
    
    private function getSetsConfig($type, $request){
        $configs = array(
            'Best' => array('id' => 'Best', 'title' => LangAdmin::get('Recommended_products'), 'ItemRatingType' => 'Best' ,'categoryId' => 0, 'isEditable' => true, 'isOrderEditable' => true),
            'Popular' => array('id' => 'Popular', 'title' => LangAdmin::get('Popular_products'), 'ItemRatingType' => 'Popular' , 'categoryId' => 0, 'isEditable' => false, 'isOrderEditable' => false),
            'Last' => array('id' => 'Last', 'title' => LangAdmin::get('Last_viewed'), 'ItemRatingType' => 'Last' , 'categoryId' => 0, 'isEditable' => false, 'isOrderEditable' => false),
            'Warehouse' => array('id' => 'Warehouse', 'title' => LangAdmin::get('Warehouse'), 'ItemRatingType' => 'Best', 'categoryId' => 'Warehouse', 'isEditable' => true, 'isOrderEditable' => true),
            'Category' => array('id' => 'Category', 'title' => LangAdmin::get('Category'), 'ItemRatingType' => 'Category' , /*'categoryId' => 0,*/ 'isEditable' => true, 'isOrderEditable' => true), // для данного типа categoryId берется из гет параметра
        );
        
        $config = $configs[$type];

        if (! isset($config['categoryId'])) {
            $categoryId = $request->getValue('cid') ? $request->getValue('cid') : 0;
            $config['categoryId'] = $categoryId; 
            // get category title
            if ($categoryId) {
                // получаем название категории
                try {
                    $category = $this->otapilib->GetCategoryInfo($categoryId);
                    $categoryName = $category['name'];
                } catch (Exception $e) {
                    $categoryName = $categoryId; // если категория удалена имя = id
                }

                $config['title'] = $config['title'] . ': ' . $categoryName;
            }
        }
        
        return $config;        
    }
    
    public function itemsAction($request)
    {

        $this->_template = 'items';
        $type = ucfirst(strtolower($request->getValue('type', 'best')));
        $config = $this->getSetsConfig($type, $request);
        $contentType = 'Item';
        $totalCount = 0;
        
        try {
            $data = $this->setsProvider->GetItemRatingList($config['ItemRatingType'], 50, $config['categoryId'], $this->getActiveLang($request), 0);
            $items = $data['items'];
            $totalCount = $data['totalCount'];
            
            $items = $this->setsProvider->GetItemCustomPictures($type, $items, $this->getActiveLang($request));
            
            $this->tpl->assign('items', $items);
            $this->tpl->assign('contentType', $contentType);
            $this->tpl->assign('totalCount', $totalCount);
            $this->tpl->assign('id', $config['id']);
            $this->tpl->assign('type', $type);
            $this->tpl->assign('categoryId', $config['categoryId']);
            $this->tpl->assign('title', $config['title']);
            $this->tpl->assign('isEditable', $config['isEditable']);
            $this->tpl->assign('isOrderEditable', $config['isOrderEditable']);
            $this->tpl->assign('languages', $this->languagesProvider->GetActiveLanguages());
            $this->tpl->assign('currentLang', $this->getActiveLang($request));
        }
        catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }
        
        print $this->fetchTemplate();
    }

    public function clearSetRecommendedCategoriesAction()
    {
        $sid = Session::get('sid');
        $res = null;
        try {
            OTAPILib2::RemoveAllElementsRatingList($sid, 'Best', 'Category', 0, $res);
            OTAPILib2::makeRequests();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse();
    }

    public function moreRecommendedCategoriesAction($request)
    {
        $this->_template = 'morecategories';
        $categoriesArray = array();
        $size = 20;
        $offset = $request->getValue('offset');
        $language = $this->getActiveLang($request);
        try {
            $xmlParams = '<BatchRatingListSearchParameters><RatingLists><RatingList><CategoryId>0</CategoryId><ItemRatingType>Best</ItemRatingType><IsRandomSearch>false</IsRandomSearch><ContentType>Category</ContentType><FramePosition>' . $offset . '</FramePosition><FrameSize>' . $size . '</FrameSize></RatingList></RatingLists></BatchRatingListSearchParameters>';
            /** @var $setsData OtapiBatchRatingListsSearchResultAnswer */
            OTAPILib2::BatchSearchRatingLists($language, $xmlParams,$setsData);
            OTAPILib2::makeRequests();

            $res =  $setsData->GetResult()->GetCategories()->GetRatingList();

            foreach ($res as $value) {
                    $totalCount = $value->GetResult()->GetTotalCount();
                foreach ($value->GetResult()->GetContent()->GetItem() as $item) {
                    $categoriesArray[] = $item;
                }
            }

            $this->tpl->assign('languages', $this->languagesProvider->GetActiveLanguages());
            $this->tpl->assign('restrictionLanguage', $language);
            $this->tpl->assign('listCategories', $categoriesArray);
            $this->tpl->assign('totalCount', $totalCount);
            $result = $this->fetchTemplateWithoutHeaderAndFooter(false);

        } catch (Exception $e){
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse(array("html" => $result));

    }
    
    public function moreItemsAction($request)
    {
        $this->_template = 'moreitems';
        $type = ucfirst(strtolower($request->getValue('type', 'best')));
        $config = $this->getSetsConfig($type, $request);
        $offset = $request->getValue('offset', 51);
        $count = 50;
        $result = '';
        $language = $this->getActiveLang($request);

        try {
            $data = $this->setsProvider->GetItemRatingList($config['ItemRatingType'], $count, $config['categoryId'], $language, $offset);
            $items = $data['items'];
            $totalCount = $data['items'];

            $items = $this->setsProvider->GetItemCustomPictures($type, $items, $this->getActiveLang($request));

            $this->tpl->assign('items', $items);
            $this->tpl->assign('totalCount', $totalCount);
            $this->tpl->assign('type', $type);
            $this->tpl->assign('categoryId', $config['categoryId']);
            $this->tpl->assign('title', $config['title']);
            $this->tpl->assign('isEditable', $config['isEditable']);
            $this->tpl->assign('isOrderEditable', $config['isOrderEditable']);
            $this->tpl->assign('languages', $this->languagesProvider->GetActiveLanguages());
            $this->tpl->assign('currentLang', $this->getActiveLang($request));

            $result = $this->fetchTemplateWithoutHeaderAndFooter(false);
        }
        catch (ServiceException $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array('html' => $result));
    }

    public function getItemInfoAction($request)
    {
        $data = array();
        try {
            $id = $request->getValue('id');

            $itemInfo = $this->setsProvider->GetItemFullInfo($id, $request->getValue('language'));
            $data['title'] = $itemInfo['Title'];

            $itemDescription = $this->setsProvider->GetItemDescription($id, $request->getValue('language'));
            $data['description'] = (string)$itemDescription;

            $data['result'] = 'ok';
        }
        catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse($data);
    }

    public function addSetsBrandAction($request) 
    {
        $brands = array();
        try {
            $ids = array();
            $sid = Session::get('sid');
            $urlId = $request->getValue('urlId');
            $language = $this->getActiveLang($request);

            $ids = $this->parseItemListOrUrl($urlId, 'brand');

            $resultIds = array();
            foreach ($ids as $i => $id) {
                $result = $this->setsProvider->AddElementsSetToRatingList($sid, 'Best', 'Brand', 0, $id);
                if ($result) {
                    $resultIds[$id] = true;
                }
            }
            $brandsList = $this->setsProvider->GetBrandRatingList('Best', 100, 0, $language);
            foreach ($brandsList as $i => &$brand) {
                if (array_key_exists($brand['id'], $resultIds)) {
                    $brand['BrandUrl'] = UrlGenerator::generateBrandUrl($brand['id']);
                    $brand['brandurl'] = $brand['BrandUrl'];
                    $brands[] = $brand;
                }
            }
            SetsUpdater::clearCachePart($this->getActiveLang($request), 'Brand');
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array('result' => 'ok', 'brands' => $brands));
    }

    private function getVendorId($VendorData)
    {
        if (strpos(trim($VendorData), 'http://') === 0 || strpos(trim($VendorData), 'https://') === 0) {
            $urlComponents = parse_url(trim($VendorData));
            parse_str($urlComponents['query'], $queryArray);
            $data = isset($queryArray['id']) ? $queryArray['id'] : false;
            if (!$data) {
                $data = isset($queryArray['vid']) ? $queryArray['vid'] : false;
            }
        } else {
            $data = $VendorData;
        }
        return $data;
    }

    public function updateSetsRecommendedCategoryAction($request)
    {
        try {
            $id = $request->getValue('itemId');
            $type = 'Best';
            $cid = 0;
            $oldImage = $request->getValue('existingImage');
            $imageUrl = $oldImage;
            $newImage = $this->getNameSetUploadImage();

            $xml = new SimpleXMLElement('<EditableCategoryInfo></EditableCategoryInfo>');

            if ($newImage) {
                $xml->addChild('IconImageUrl', $newImage);
            } elseif ($oldImage == 'del') {
                $xml->addChild('IconImageUrl', "");
            }

            $this->categoriesNewProvider->initEditCategoryInfo('ru', $id, $xml->asXML());
            $this->categoriesNewProvider->doRequests();
            OTAPILib2::GetCategoryInfo('ru', $id, $result);
            OTAPILib2::makeRequests();
            $categoryImage = $result->GetOtapiCategory()->GetIconImageUrl();
            SetsUpdater::clearCachePart($this->getActiveLang($request), 'Category', $type, $cid);

        } catch(ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array('result' => 'ok', 'picture' => $imageUrl, 'newImage' => $categoryImage));
    }


    public function addRecommendedCategoryAction($request)
    {
        $sid = Session::get('sid');
        $categoryId =  $request->getValue('categoryId');
        try {
            $this->setsProvider->AddElementsSetToRatingList($sid, "Best", "Category", 0, $categoryId);
        } catch (Exception $e) {
            $this->respondAjaxError($e->getErrorMessage());
        }
        $this->sendAjaxResponse();
    }

    public function checkVendorAction($request) {
        $url = $request->getValue('sellerId');
        $vendorId = $this->getVendorId($url);
        $alias = $request->getValue('alias');
        $issetName =  $this->categoriesProvider->checkVendorName($vendorId);

        if ($issetName) {
            $this->respondAjaxError(LangAdmin::get('Duplicate_vendor_name'));
        }
        if (!empty($alias)) {
            $issetAlias = $this->categoriesProvider->checkVendorAlias($alias, $url);
            if ($issetAlias) {
                $this->respondAjaxError(LangAdmin::get('Duplicate_vendor_alias'));
            }
        }
    }

    public function addSetsSellerAction($request)
    {
        $this->checkVendorAction($request);
        $sellers = array();
        try {
            $sid = Session::get('sid');
            $adminLang = Session::getActiveAdminLang();

            $alias = $request->getValue('alias');
            $language = $request->getValue('language');
            $sellerId = $request->getValue('sellerId');
            $displayName = $request->getValue('displayName');

            $pageTitle = $request->getValue('pagetitle');
            $seoKeywords = $request->getValue('seo-keywords');
            $seoDescription = $request->getValue('seo-description');

            $validator = new Validator(array('sellerId' => trim($sellerId)));
            $validator->addRule(new NotEmptyString(), 'sellerId', LangAdmin::get('Vendor_Id_cannot_be_empty'));
            if (! $validator->validate()) {
                $this->respondAjaxError($validator->getErrors());
            }

            $vendorId = $this->getVendorId($sellerId);
            $vendorExists = $this->setsProvider->GetVendorInfo($vendorId);
            if ($vendorExists === false ) {
                throw new ServiceException(__METHOD__, '', LangAdmin::get('Vendor_not_found'), 1);
            }

            $addResult = $this->setsProvider->AddElementsSetToRatingList($sid, 'Best', 'Vendor', 0, $vendorId);
            if ($addResult === false) {
                throw new ServiceException(__METHOD__, '', LangAdmin::get('Internal_error'), 1);
            }

            $uploadedFile = [];
            $uploadedFileId = '';
            $uploadedFileUrl = '';
            if (! empty($_FILES['seller_image']['name'])) {
                $fileType = 'Image';
                $uploaderOptions = ['param_name' => 'seller_image'];
                $uploader = new OTFileStorage($language, $fileType, $uploaderOptions, null, 'vendors_uploads');
                $uploadedFile = $uploader->post();
                $uploadedFileId = $uploadedFile['seller_image'][0]->fileId;
                $uploadedFileUrl = $uploadedFile['seller_image'][0]->url;
            }

            $xmlParams = new SimpleXMLElement('<AdditionalVendorInfoUpdateData></AdditionalVendorInfoUpdateData>');
            $xmlParams->addChild('Name', $displayName);
            if (! empty($uploadedFileId)) {
                $xmlParams->addChild('Image')->addChild('Value')->addAttribute('Id', $uploadedFileId);
            }
            $xmlParams = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
            OTAPILib2::UpdateAdditionalVendorInfo($adminLang, $sid, $this->getActiveLang($request), $vendorId, $xmlParams, $answer);
            OTAPILib2::makeRequests();

            $this->setsProvider->saveSetSellerInfo($vendorId, $alias, $language);

            $pageUrl = new AdminUrlWrapper();
            $pageUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
            $seller['editUrl'] = $pageUrl->AssignCmdAndDo('sets', 'editSeller') . '&id=' . $vendorId;
            $sellersList = $this->setsProvider->GetVendorRatingList('Best', 200, 0, $language);
            foreach ($sellersList as $i => &$seller) {
                if ($seller['id'] == $vendorId) {
                    $seller['url'] = UrlGenerator::generateSearchUrlByParams(['vid' => $seller['id']]);
                    $seller['editUrl'] = $pageUrl->AssignCmdAndDo('sets', 'editSeller') . '&id=' . $vendorId;
                    $seller['displayName'] = $displayName ? $displayName : $seller['name'];
                    if (! empty($uploadedFileUrl)) {
                        $seller['PictureUrl'] = $uploadedFileUrl;
                    }
                    $sellers[] = $seller;
                }
            }
            $this->categoriesProvider->setVendorSEO(array('sellerId' => $vendorId, 'meta_title' => $pageTitle, 'meta_keywords' => $seoKeywords, 'meta_description' => $seoDescription, 'seolanguage' => $language));

            SetsUpdater::clearCachePart($this->getActiveLang($request), 'Vendor');
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getErrorMessage());
        }

        $this->sendAjaxResponse(array('result' => 'ok', 'sellers' => $sellers));
    }

    public function editSellerAction($request)
    {
        $this->_template = 'sellers_crud/crud';

        try {
            $sid = Session::get('sid');
            $lang = $this->getActiveLang($request);
            $inputLang = $this->getActiveLang($request);
            $vendorId = urldecode($request->getValue('id'));

            OTAPILib2::GetAdditionalVendorInfo($lang, $sid, $inputLang, $vendorId, 'true', $answer);
            OTAPILib2::makeRequests();

            $entities = $answer->GetResult()->GetRawData();
            $updateSettingsUrl = '?cmd=Sets&do=updateSetsSellerInOtapilib&metaEntity=AdditionalVendorInfo&vendorId=' . $vendorId;
            $settings = MetaUI::render($entities, $updateSettingsUrl);

            $vendorInfo = $this->setsProvider->getSetSellerInfo($vendorId, $inputLang);
            $vendorInfo['id'] = $vendorId;
            $vendorInfo['seo'] = $this->categoriesProvider->getVendorSEO($vendorId, $inputLang);
            if (! $vendorInfo['seo']) {
                $vendorInfo['seo'] = array('pagetitle' => '', 'seo_keywords' => '', 'seo_description' => '', 'type' => '');
            }
        } catch (Exception $e) {
            ErrorHandler::registerError($e);
        }

        $this->tpl->assign('vendorInfo', $vendorInfo);
        $this->tpl->assign('currentLang', $inputLang);
        $this->tpl->assign('settings', $settings);

        $pageUrl = new AdminUrlWrapper();
        $this->tpl->assign('pageUrl', $pageUrl);

        print $this->fetchTemplate();
    }

    public function updateSetsSellerInOtapilibAction($request)
    {
        try {
            $sid = Session::get('sid');
            $lang = Session::getActiveAdminLang();
            $vendorId = urldecode($request->get('vendorId'));
            $inputLang = $this->getActiveLang($request);
            $name = $request->post('name');
            $value = $request->post('value');
            $type = $request->getValue('type');

            $params = explode(MetaUI::NODES_SEPARATOR, $name);
            if (is_array($params) && count($params) > 0) {
                $xmlParameters = MetaUI::generateSingleParamXml('AdditionalVendorInfoUpdateData', $params, $value, $type);

                OTAPILib2::UpdateAdditionalVendorInfo($lang, $sid, $inputLang, $vendorId, $xmlParameters, $answer);
                OTAPILib2::makeRequests();
            }

        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }

        $this->sendAjaxResponse(array(), true);
    }

    public function updateSetsSellerAction($request)
    {
        try {
            $language = $request->getValue('language');
            $sellerId = $request->getValue('sellerId');
            $pageTitle = $request->getValue('pagetitle');
            $prefix = $request->getValue('prefix');
            $suffix = $request->getValue('suffix');
            $seoKeywords = $request->getValue('seo-keywords');
            $seoDescription = $request->getValue('seo-description');
            $alias = $request->getValue('url');
            if (!empty($alias)) {
                if ($this->categoriesProvider->checkVendorAlias($alias, $sellerId)) {
                    $this->respondAjaxError(LangAdmin::get('Duplicate_vendor_alias'));
                }
            }
            $validator = new Validator(array(
                'sellerId' => trim($sellerId),
            ));

            $validator->addRule(new NotEmptyString(), 'sellerId', LangAdmin::get('Vendor_Id_cannot_be_empty'));
            if (! $validator->validate()) {
                $this->respondAjaxError($validator->getErrors());
            }
            $vendor = $this->setsProvider->GetVendorInfo($sellerId);

            if ($vendor === false ) {
                throw new ServiceException(__METHOD__, '', LangAdmin::get('Vendor_not_found'), 1);
            }

            $info = $this->setsProvider->getSetSellerInfo($sellerId, $language);
            if ($info) {
                $this->setsProvider->updateSetSellerInfo($sellerId, $alias, $language);
            } else {
                $this->setsProvider->saveSetSellerInfo($sellerId, $alias, $language);
            }

            SetsUpdater::clearCachePart($this->getActiveLang($request), 'Vendor');

            $this->categoriesProvider->setVendorSEO(array('sellerId' => $sellerId, 'seo_title' => $prefix . '||' . $suffix , 'meta_title' => $pageTitle, 'meta_keywords' => $seoKeywords, 'meta_description' => $seoDescription, 'seolanguage' => $language));
        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }
    }

    private function uploadImage($param_name)
    {
        ob_start();
        new UploadHandler(array(
            'param_name' => $param_name,
            'image_versions' => array(
                'thumbnail_100_100' => array(
                    'max_width' => 100,
                    'max_height' => 100,
                    'jpeg_quality' => 90
                ),
                'thumbnail_160_160' => array(
                    'max_width' => 160,
                    'max_height' => 160,
                    'jpeg_quality' => 90
                ),
                'thumbnail_310_310' => array(
                    'max_width' => 310,
                    'max_height' => 310,
                    'jpeg_quality' => 90
                ),
            ),
        ), true, null, '/uploaded/sets/');
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

    private function uploadData()
    {
        ob_start();
        new UploadHandler(array(
            'param_name' => 'itemsFile',
            'accept_file_types' => '/\.(txt)$/i'
        ), true, null, '/uploaded/sets/');
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

    // parse item url or list like: id;id;id
    private function parseItemListOrUrl($urlId, $itemName)
    {
        $ids = array();

        if (preg_match('/(http|https)/i', $urlId)) {
            if ($url = parse_url($urlId)) {
                if(isset($url['query'])) {
                    $params = array();
                    parse_str($url['query'], $params);
                    if (isset($params['id'])) {
                        $ids[] = trim($params['id'], '_');
                    } elseif (isset($params[$itemName])) {
                        $ids[] = $params[$itemName];
                    }
                } else {
                    $ids[] = trim($urlId);
                }
            } else {
                $ids[] = trim($urlId);
            }
        } else {
            $ids = explode(';', $urlId);
        }

        return $ids;
    }

    private function generatePackages($ids, $frameSize)
    {
        if ( !is_array($ids)) {
            $ids = array($ids);
        }
        $itemFrames = array();
        $itemFrame = array();
        $itemCount = 0;

        foreach ($ids as $id) {
            if ($itemCount < $frameSize) {
                // Добавить элемент к пакету
                $itemFrame[] = $id;
                $itemCount++;
            } else {
                // Создать новый пакет и добавить в него элемент
                $itemFrames[] = $itemFrame;
                $itemFrame = array();
                $itemFrame[] = $id;
                $itemCount = 1;
            }
        }
        // Добавить не полный пакет если он есть
        if (!empty($itemFrame)) {
            $itemFrames[] = $itemFrame;
        }

        return $itemFrames;
    }

    private function formattingAddedAndNotAddedItems($newItemsIds, $type, $cid, $title, $sid, $request)
    {
        $items = array();
        $method = "GetItemRatingList";

        $data = $this->setsProvider->$method($type, 200, $cid, $this->getActiveLang($request));

        foreach ($data['items'] as $i => $item) {
            if (in_array($item['id'], $newItemsIds)) {
                if (! empty($title)) {
                    $key = "taobao:Item:Title";
                    if (!$this->warehouseProvider->IsWarehouseItem($item['id'], $this->getActiveLang($request))) {
                        $r = $this->setsProvider->EditTranslateByKey($sid, $this->getActiveLang($request), $title, $key, $item['id']);
                    } else {
                        $data = array('title' => $title);
                        $xml = $this->warehouseProvider->CreateWarehouseItemSimpleXML($data);
                        $id = trim($item['id'], 'wh-');
                        $this->warehouseProvider->UpdateWarehouseItem(Session::get('sid'), $id, $xml);
                    }
                    $item['Title'] = $title;
                    $item['title'] = $title;
                }
                $items[] = $item;
            }
        }
        return $items;
    }

    // add item to sets by id and type
    private function addSetsItem($type, $ids, $contentType, $cid = 0, $title = null, $frameSize = 1, $request)
    {
        $sid = Session::get('sid');
        $result = array();
        $errors = array();

        $itemFrames = $this->generatePackages($ids, $frameSize);

        foreach ($itemFrames as $itemFrame) {
            try {
                $itemFrameList = implode(';', $itemFrame);
                $res = $this->setsProvider->AddElementsSetToRatingList($sid, $type, $contentType, $cid, $itemFrameList);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        $items = $this->formattingAddedAndNotAddedItems($ids, $type, $cid, $title, $sid, $request);
        return array($items, $errors);
    }

    // add sets item by id and type
    public function addSetsItemAction($request)
    {
        $items = array();
        $errors = array();
        try {
            $urlId = $request->getValue('urlId');
            $title = $request->getValue('title');
            $type = ucfirst(strtolower($request->getValue('type', 'best')));
            $cid = $request->getValue('cid', false);
            $ids = $this->parseItemListOrUrl($urlId, 'item');
            $config = $this->getSetsConfig($type, $request);

            list($items, $errors) = $this->addSetsItem($config['ItemRatingType'], $ids, 'Item', $config['categoryId'], $title, 1, $request);

            SetsUpdater::clearCachePart($this->getActiveLang($request), 'Item', $config['ItemRatingType'], $cid);
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse(array(
            'items' => $items,
            'errors' => $errors
        ));
    }

    public function addSetsItemsFileAction($request)
    {
        $items = array();
        $errors = array();
        try {
            $type = $request->getValue('type', 'Best');
            $title = $request->getValue('title', null);
            $config = $this->getSetsConfig($type, $request);

            $uploadResult = json_decode($this->uploadData());
            if (isset($uploadResult->itemsFile[0]->uploaded_url)) {
                $itemsFile = $uploadResult->itemsFile[0]->uploaded_url;
                $content = file_get_contents($itemsFile);
                if ($uploadResult->itemsFile[0]->type == 'text/plain' ) {
                    $rows = explode("\n", $content);

                    $allIds = array();
                    foreach ($rows as $key => $item) {
                        if (trim($item) !== '') {
                            $ids = $this->parseItemListOrUrl($item, 'item');
                            $allIds = array_merge($allIds, $ids);
                        }
                    }

                    // add item to rating
                    list($items, $errors) = $this->addSetsItem($config['ItemRatingType'], $allIds, 'Item', $config['categoryId'], $title, 100, $request);
                }

                SetsUpdater::clearCachePart($this->getActiveLang($request), 'Item', 'Best');
            } else {
                $this->respondAjaxError(LangAdmin::get('Select_text_file_with_product_links'));
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse(array(
            'items' => $items,
            'errors' => $errors
        ));
    }

    // delete item by type and id
    public function deleteItemAction($request)
    {
        try {
            $sid = Session::get('sid');
            $itemList = $request->getValue('id');
            $cid = $request->getValue('cid', 0);
            $type = ucfirst(strtolower($request->getValue('type', 'Best')));
            $contentType = ucfirst(strtolower($request->getValue('contentType', 'Item')));

            // при удалении продовца удалим кастомную картинку и название
            if ($contentType === 'Vendor') {
                $this->setsProvider->deleteSetSellerInfo($itemList, Session::getActiveAdminLang());
            }

            $config = $this->getSetsConfig($type, $request);

            $result = $this->setsProvider->RemoveElementsSetRatingList($sid, $config['ItemRatingType'], $contentType, $cid, $itemList);

            SetsUpdater::clearCachePart($this->getActiveLang($request), $contentType, $config['ItemRatingType'], $cid);
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    public function updateSetsItemAction($request)
    {
        try {
            $sid = Session::get('sid');
            $id = $request->getValue('itemId');
            $title = $request->getValue('displayName');
            $description = $request->getValue('description');
            $type = $request->getValue('type', 'Best');
            $cid = $request->getValue('cid', 0);

            $oldImage = $request->getValue('existingImage');
            $imageUrl = $oldImage;
            $newImage = $this->getNameSetUploadImage();
            $lang = $this->getActiveLang($request);

            if ($newImage) {
                $imageUrl = $newImage;
                $this->setsProvider->SetItemCustomPictures($type, $id, $newImage, $request->getValue('language'));
            } elseif ($oldImage == 'del') {
                $this->setsProvider->DelItemCustomPictures($type, $id, $request->getValue('language'));
                $imageUrl = $request->getValue('originalPicture');
            }

            if ($this->warehouseProvider->IsWarehouseItem($id, $lang)) {
                $data = array('title' => $title, 'newImage' => $newImage, 'description' => $description);
                $xml = $this->warehouseProvider->CreateWarehouseItemSimpleXML($data);
                $validator = new Validator(array(
                    'Id'            => $id,
                    'Name'          => $title,
                    'Description'   => $description,
                ));
                $data = $validator->getData();
                $idWarehouseItem = trim($data['Id'], 'wh-');
                $this->warehouseProvider->UpdateWarehouseItem(Session::get('sid'), $idWarehouseItem, $xml);
            } else {
                $key = "taobao:Item:Title";
                $result = $this->setsProvider->EditTranslateByKey($sid, $request->getValue('language'), $title, $key, $id);

                $key = "taobao:Item:Description";
                $result = $this->setsProvider->EditTranslateByKey($sid, $request->getValue('language'), $description, $key, $id);
            }
            SetsUpdater::clearCachePart($this->getActiveLang($request), 'Item', $type, $cid);
        } catch(Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array('result' => 'ok', 'picture' => $imageUrl));
    }

    public function getActiveLang($request)
    {
        if ($request->getValue('language')) {
            return $request->getValue('language');
        }

        $cmd = RequestWrapper::get('cmd');
        if (RequestWrapper::getValueSafe('language')) {
            return RequestWrapper::getValueSafe('language');
        } elseif (Session::get('active_lang_' . strtolower($cmd))) {
            return Session::get('active_lang_' . strtolower($cmd));
        } else {
            return key($this->languagesProvider->GetActiveLanguages());
        }
    }

    private function uploadSetImage()
    {
        $uploader = new UploadHandler(array(
            'param_name' => 'newImage',
            'image_versions' => array(
                '' => array(
                    'max_width' => 800,
                    'max_height' => 600,
                    'jpeg_quality' => 95
                ),
            ),
        ), false, null, '/uploaded/sets/');
        return $uploader->post(false);
    }

    private function getNameSetUploadImage()
    {
        if (! empty($_FILES['newImage']['tmp_name'])) {
            $uploadResult = $this->uploadSetImage();
            if (isset($uploadResult['newImage'][0])) {
                if (isset($uploadResult['newImage'][0]->url)) {
                    $logoUrl = $uploadResult['newImage'][0]->url;
                } else if (isset($uploadResult['newImage'][0]->error)) {
                    $this->respondAjaxError($uploadResult['newImage'][0]->error);
                }
            } else {
                $this->respondAjaxError('Unknown error occured while uploading image. Try again.');
            }
        } else {
            $logoUrl = '';
        }
        return $logoUrl;
    }

    /** @var $request RequestWrapper */
    public function recommendedCategoryAction($request)
    {
        $language = $this->getActiveLang($request);
        $sid = Session::get('sid');
        $this->_template = 'category';
        $from = 0;
        $size = 20;
        $categoriesArray = array();
        $totalCount = 0;
        try {
            $categories = $this->categoriesProvider->GetEditableCategorySubcategories($sid, 0, 'true');

            $xmlParams = '<BatchRatingListSearchParameters><RatingLists><RatingList><CategoryId>0</CategoryId><ItemRatingType>Best</ItemRatingType><IsRandomSearch>false</IsRandomSearch><ContentType>Category</ContentType><FramePosition>' . $from . '</FramePosition><FrameSize>' . $size . '</FrameSize></RatingList></RatingLists></BatchRatingListSearchParameters>';
            /** @var $setsData OtapiBatchRatingListsSearchResultAnswer */
            OTAPILib2::BatchSearchRatingLists($language, $xmlParams,$setsData);
            OTAPILib2::makeRequests();
            $res =  $setsData->GetResult()->GetCategories()->GetRatingList();

            foreach ($res as $value) {
            $totalCount = $value->GetResult()->GetTotalCount();
            foreach ($value->GetResult()->GetContent()->GetItem() as $item) {
                $categoriesArray[] = $item;
            }
     }
            $this->tpl->assign('languages', $this->languagesProvider->GetActiveLanguages());
            $this->tpl->assign('activeLanguage', $language);
            $this->tpl->assign('listCategories', $categoriesArray);
            $this->tpl->assign('totalCount', $totalCount);
            $this->tpl->assign('categories', $categories);

        } catch (Exception $e){
            $this->errorHandler->registerError($e);
        }

        print $this->fetchTemplate();
    }

    public function deleteRecommendedCategoryAction($request)
    {
        $sid = Session::get('sid');
        $categoryId = $request->getValue('categoryId');
        $contentType = "Category";
        $cid = 0;

        try {
            $this->setsProvider->RemoveElementsSetRatingList($sid, 'Best', $contentType, $cid, $categoryId);
        } catch (Exception $e){
            $this->respondAjaxError($e->getErrorMessage());
        }
        $this->sendAjaxResponse();
    }

    public function categoriesAction()
    {
        $this->_template = 'categories';

        try {
            $sid = Session::get('sid');
            $siteCategories = $this->setsProvider->GetSiteSetCategories($sid);
            $this->tpl->assign('siteCategories', $siteCategories);
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }

        print $this->fetchTemplate();
    }
    
    public function getAllCategoriesAction($request)
    {
        $this->_template = 'allcategories';
        
        try {
            $sid = Session::get('sid');
            $perpage = 10;
            $page = $request->get('page', 1);
            $allCategories = $this->setsProvider->GetAllSetCategories($sid, ($page-1) * $perpage, $perpage);
            $this->tpl->assign('allCategories', $allCategories['list']);
            $this->tpl->assign('totalCount', $allCategories['totalCount']);
            $this->tpl->assign('currentPage', $page);
            $this->tpl->assign('paginator', new Paginator($allCategories['totalCount'], $page, $perpage));
            
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }
        
        $this->sendAjaxResponse(array(
            'result' => 'ok',
            'list' => $this->fetchTemplateWithoutHeaderAndFooter(),
        ));
    }

    public function categoriesSettingsAction($request)
    {
        $this->_template = 'categories-settings';

        print $this->fetchTemplate();
    }
    
    public function autosetsAction($request)
    {
        $this->_template = 'autosets';
    
        try {
            $lang = Session::getActiveAdminLang();
            $sid = Session::get('sid');
            $settings = array();
            OTAPILib2::GetAutoRatingListsSettings($lang, $sid, 'true', $settings);
            OTAPILib2::makeRequests();
            
            $settings = $settings->GetResult()->GetRawData();
            
            $this->tpl->assign('settings', $settings);
            $this->tpl->assign('updateUrl', '?cmd=Sets&do=updateAutosetsSettings');
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }

        print $this->fetchTemplate();
    }
    
    public function startAutosetsScanAction($request)
    {
        $activityId = '';
        $activityType = '';
        try {
            $lang =  Session::getActiveAdminLang();
            $sessionId = Session::get('sid');
                
            $answer = array();
            OTAPILib2::RunAutoRatingListsUpdating($lang, $sessionId, $answer);
            OTAPILib2::makeRequests();
        
            if (! $answer) {
                throw new Exception('Service reply is wrong');
            }
        
            $activityId = $answer->getResult()->GetId()->asString();
            $activityType = $answer->getResult()->GetType();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        
        $this->sendAjaxResponse(array('result' => 'ok', 'activityId' => $activityId, 'activityType' => $activityType), true);        
    }
    
    public function updateAutosetsSettingsAction($request)
    {
        $name = $request->post('name');
        $value = $request->post('value');
        $type = $request->get('type');
    
        try {
            $params = explode(MetaUI::NODES_SEPARATOR, $name);
            if (is_array($params) && count($params) > 0) {
                $providerType = $request->get('providerType');
                $xmlParameters = MetaUI::generateSingleParamXml('AutoRatingListsSettingsUpdateData', $params, $value, $type);
                $answer = false;
                OTAPILib2::UpdateAutoRatingListsSettings(Session::getActiveAdminLang(), Session::get('sid'), $xmlParameters, $answer);
                OTAPILib2::makeRequests();
    
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array(), true);
    }    

    public function saveSiteCategoriesAction($request)
    {
        try {
            $ids = $request->getValue('ids');
            if (!is_array($ids)) {
                $ids = array();
            }

            $this->setsProvider->SetSiteCategoriesSet($ids);

            $this->sendAjaxResponse(array('result' => 'ok'), true);
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array('result' => 'ok'), true);
    }


    public function setRecommendedCategoryPositionAction($request)
    {
        try {
            $sid = Session::get('sid');
            $contentType = 'Category';
            $type = "Best";
            $id = $request->getValue('id');
            $cid = 0;
            $position = $request->getValue('position', 0);

            $this->setsProvider->setItemPosition($this->getActiveLang($request), $sid, $type, $contentType, $cid, $id, $position);

            SetsUpdater::clearCachePart($this->getActiveLang($request), $contentType, $type, $cid);
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array('result' => 'ok'));
    }


    public function setItemPositionAction($request)
    {
        try {
            $contentType = 'Item';
            $type = $request->getValue('type', 'Best'); //Best, Last, Popular etc.
            $id = $request->getValue('id');
            $cid = $request->getValue('cid', 0);
            $position = $request->getValue('position', 0);
            $config = $this->getSetsConfig($type, $request);
            
            $this->setsProvider->setItemPosition($this->getActiveLang($request), Session::get('sid'), $config['ItemRatingType'], $contentType, $cid, $id, $position);
            
            SetsUpdater::clearCachePart($this->getActiveLang($request), $contentType, $config['ItemRatingType'], $cid);
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array('result' => 'ok'));
    }

    public function exportAction($request)
    {
        $this->_template = 'export';

        try {
            $lang = Session::getActiveAdminLang();
            $sid = Session::get('sid');
            $targets = array();
            OTAPILib2::GetSelectorExportingTargets($lang, $sid, $targets);
            $settings = array();
            OTAPILib2::GetSelectorExportingSettings($lang, $sid, 'true', $settings);
            OTAPILib2::makeRequests();

            $targets = $targets->GetResult()->GetContent()->GetItem();
            $settings = $settings->GetResult()->GetRawData();

            $this->tpl->assign('targets', $targets);
            $this->tpl->assign('settings', $settings);
            $this->tpl->assign('updateUrl', '?cmd=Sets&do=updateSelectorExportingSettings');
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }
        print $this->fetchTemplate();
    }

    public function updateSelectorExportingSettingsAction($request)
    {
        $name = $request->post('name');
        $value = $request->post('value');
        $type = $request->get('type');

        try {
            $params = explode(MetaUI::NODES_SEPARATOR, $name);
            if (is_array($params) && count($params) > 0) {
                $xmlParameters = MetaUI::generateSingleParamXml('SelectorExportersSettingsUpdateData', $params, $value, $type);
                $answer = false;
                OTAPILib2::UpdateSelectorExportingSettings(Session::getActiveAdminLang(), Session::get('sid'), $xmlParameters, $answer);
                OTAPILib2::makeRequests();
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array(), true);
    }

    public function startExportScanAction($request)
    {
        $activityId = '';
        $activityType = '';
        try {
            $lang = Session::getActiveAdminLang();
            $sessionId = Session::get('sid');
            $exportingTarget = $request->post('exportingTarget');
            $answer = null;
            OTAPILib2::RunSelectorExporting($lang, $sessionId, $exportingTarget, $answer);
            OTAPILib2::makeRequests();

            if (!$answer) {
                throw new Exception('Service reply is wrong');
            }

            $activityId = $answer->getResult()->GetId()->asString();
            $activityType = $answer->getResult()->GetType();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse(array('result' => 'ok', 'activityId' => $activityId, 'activityType' => $activityType), true);
    }
}
