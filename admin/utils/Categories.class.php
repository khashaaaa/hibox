<?php

OTBase::import('system.lib.Cache');
OTBase::import('system.lib.cache.Key');
OTBase::import('system.lib.cache.adapter.*');
OTBase::import('system.uploader.php.UploadHandler');
OTBase::import('system.lib.Validation.*');
OTBase::import('system.lib.Validation.Rules.*');

class Categories extends GeneralUtil
{
    protected $_template = 'categories';
    protected $_template_path = 'categories/';

    protected $categoriesProvider;
    protected $categoriesNewProvider;
    protected $cacher;
    protected $existingCategory = null;
    protected $existingCategoriesAliases = null;
    protected $existingSeoText = null;
    protected $existingCategorySEO = null;
    protected $insertAliasesArray = array();
    protected $insertSEOArray = array();
    protected $insertSEOTextArray = array();
    protected $existingLangsId = array();

    public function __construct()
    {
        parent::__construct();
        $this->categoriesProvider = new CategoriesProvider($this->cms, $this->getOtapilib());
        $this->categoriesNewProvider = new CategoriesNewProvider();
        $this->cacher = new Cache('CategoriesUISettings');
    }

    private function generateFilter($request)
    {
        $settings = $this->cacher->get('CategoriesUISettings');
        $settings = $settings ? $settings : array();

        $showHidden = isset($settings['show_hidden_categories']) ? $settings['show_hidden_categories'] : 1;

        $filter = array();
        $filter['show_hidden_categories'] = $request::post('show_hidden_categories', $showHidden);

        $settings['show_hidden_categories'] = $filter['show_hidden_categories'];
        $this->cacher->set($settings, 'CategoriesUISettings');

        return $filter;
    }

    function defaultAction($request)
    {
        $filter = $this->generateFilter($request);
        $this->tpl->assign('filter', $filter);
        $sid = Session::get('sid');

        $isSeoActive = 'false';

        if(in_array('Seo2', General::$enabledFeatures)){
            $isSeoActive = 'true';
        }
        try {
            $categories = $this->categoriesProvider->GetEditableCategorySubcategories($sid, 0, 'true');
            $providerInfoList = $this->categoriesProvider->GetProviderInfoList();
            if (! is_array($categories)) {
                throw new ServiceException(__METHOD__, '', 'Could not load categories list', 1);
            }
            if (! is_array($providerInfoList)) {
                throw new ServiceException(__METHOD__, '', 'Could not load providers list', 1);
            }
            $language = $this->getCategoriesActiveLang();
            $categories = $this->applyFilters($categories);
            $categories = $this->bindPredifenedParams($categories);
            if (is_array($categories)) {
                foreach ($categories as $k => &$category) {
                    $setsTypes = $category['AvailableItemRatingListContentTypes'];
                    $types = array();
                    foreach ($setsTypes as $k => &$setsType) {
                        $types[] = (String)$setsType;
                    }
                    $category['AvailableItemRatingListContentTypes'] = implode(',', $types);

                    $category['alias'] = $this->categoriesProvider->getCategoryAlias($category['Id']);
                    $category['seo'] = $this->categoriesProvider->getCategorySEO($category['Id'], $language);
                    $category['seo_pagetitle'] = $category['seo']['pagetitle'];
                    $category['seo_keywords'] = $category['seo']['seo_keywords'];
                    $category['seo_description'] = $category['seo']['seo_description'];
                    $category['seo_title'] = $category['seo']['seo_title'];
                }
            }

            $categoryStructure = $this->getCurrentCatalogMode();
        } catch (Exception $e) {
            ErrorHandler::registerError($e);
            $categories = array();
            $providerInfoList = array();
        }

        $this->_template = 'categories';
        $this->tpl->assign('categories', $categories);
        $this->tpl->assign('providerInfoList', $providerInfoList);
        $this->tpl->assign('isSeoActive', $isSeoActive);
        $this->tpl->assign('isRatingListForCategoryActive', CMS::IsFeatureEnabled('RatingListForCategory') ? 1 : 0);
        $this->tpl->assign('categoryStructure', $categoryStructure);
        $this->tpl->assign('catalogLanguages', $this->generateCatalogLanguages());
        print $this->fetchTemplate();
    }

    private function generateCatalogLanguages()
    {
        $activeCatalogLanguages = array();
        try {
            OTAPILib2::GetCatalogLanguageInfoList(Session::getActiveAdminLang(), Session::get('sid'), $catalogLanguages);
            OTAPILib2::makeRequests();

            if ($catalogLanguages && $catalogLanguages->GetResult()->GetContent()->GetItem()) {
                $catalogLanguages = $catalogLanguages->GetResult()->GetContent()->GetItem()->toArray();
                foreach($catalogLanguages as $value) {
                    $activeCatalogLanguages[(string)$value->getName()] = (string)$value->getDescription();
                }
            }
        } catch(ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $adminLanguages = new AdminLanguage(true);
        return $adminLanguages->getActiveLanguages($activeCatalogLanguages, false);
    }

    private function getCurrentCatalogMode() {
        $categoryStructure = array();
        $webUISettingsProvider = new WebUISettings($this->getOtapilib());
        $webUi = $webUISettingsProvider->GetWebUISettings();

        foreach ($webUi->Settings->CategoryStructureTypes->NamedProperty as $key => $typeDesc) {
            if ($typeDesc->Name == (string)$webUi->Settings->SelectedCategoryStructureType) {
                $categoryStructure = array('Name'=>(string)$typeDesc->Name, 'Desc' => (string)$typeDesc->Description);
                break;
            }
        }
        return $categoryStructure;
    }

    private function applyFilters($categories)
    {
        $settings = $this->cacher->get('CategoriesUISettings');

        $showHidden = isset($settings['show_hidden_categories']) ? $settings['show_hidden_categories'] : 1;

        $filteredCategories = array();
        $i = 0;
        foreach ($categories as $key => &$category) {
            $skip = false;

            if (($category['IsHidden'] == 'true' || $category['ishidden'] == 'true') && $showHidden == 0) {
                $skip = true;
            } elseif (($category['IsHidden'] == 'true' || $category['ishidden'] == 'true') && $showHidden == 1) {
                $category['IsHiddenUI'] = 'true';
            }

            if (! $skip) {
                $category['i'] = $i;
                $i++;
                $filteredCategories[] = $category;
            }
        }

        return $filteredCategories;
    }

    /**
     * @param RequestWrapper $request
     */
    public function getCategoriesAction($request)
    {
        $sid = Session::get('sid');
        try {
            $parentId = $request->getValue('parentId');
            $categories = $this->categoriesProvider->GetEditableCategorySubcategories($sid, $parentId, 'true');
            if (! is_array($categories)) {
                throw new ServiceException(__METHOD__, '', 'Could not load categories list', 1);
            }
            // apply filter
            $language = $this->getCategoriesActiveLang();
            $categories = $this->applyFilters($categories);
            $categories = $this->bindPredifenedParams($categories);
            if (is_array($categories)) {
                foreach ($categories as $k => &$category) {
                    $setsTypes = $category['AvailableItemRatingListContentTypes'];
                    $types = array();
                    foreach ($setsTypes as $k => &$setsType) {
                        $types[] = (String)$setsType;
                    }
                    $category['AvailableItemRatingListContentTypes'] = implode(',', $types);

                    $category['alias'] = $this->categoriesProvider->getCategoryAlias($category['Id']);
                    $category['seo'] = $this->categoriesProvider->getCategorySEO($category['Id'], $language);
                    $category['seo_pagetitle'] = $category['seo']['pagetitle'];

                    $category['seo_keywords'] = $category['seo']['seo_keywords'];
                    $category['seo_description'] = $category['seo']['seo_description'];
                    $category['seo_title'] = $category['seo']['seo_title'];
                }
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
            $categories = array();
        }

        $this->sendAjaxResponse(array(
            'categories' => $categories
        ));
    }

    /**
     * @param RequestWrapper $request
     */
    public function checkCategoryAliasAction($request)
    {
        try {
            $name = trim($request->getValue('name'));
            $alias = $request->getValue('alias', '');

            $validator = new Validator(array(
                'name' => $name,
                'alias' => $alias
            ));

            $validator->addRule(new NotEmptyString(), 'name', LangAdmin::get('Name_cannot_be_empty'));
            if (in_array('Seo2', General::$enabledFeatures) && $alias != '') {
                $validator->addAliasStringValidator('alias', LangAdmin::get('Alias_is_invalid'));
            }

            if (! $validator->validate()) {
                $this->respondAjaxError($validator->getErrors());
            }

            $aliasToSave = '';
            if (in_array('Seo2', General::$enabledFeatures)) {
                $aliasToSave = $alias != '' ? $alias : TextHelper::translitСonverter(trim($name));
                $isset = $this->categoriesProvider->checkCategoryAlias($aliasToSave);
                if ($isset) {
                    $this->respondAjaxError(LangAdmin::get('Duplicate_category_alias'));
                }
            }
        } catch (DBException  $e) {
            $message = $e->getMessage();
            if (strstr($message, 'Duplicate entry')) {
                $this->respondAjaxError(LangAdmin::get('Duplicate_category_alias'));
            } else {
                $this->respondAjaxError($e->getMessage());
            }
        }
        $this->sendAjaxResponse();
    }

    /**
     * @param RequestWrapper $request
     */
    public function createCategoryAction($request)
    {
        $sid = Session::get('sid');
        try {
            $name = $request->getValue('name');
            $alias = $request->getValue('alias', '');
            $seoText = $request->getValue('seoText', '');

            $metaPagetitle = $request->getValue('meta_pagetitle', '');
            $metaTitle = $request->getValue('meta_title', '');
            $metaKeywords = $request->getValue('meta_keywords', '');
            $metaDescription = $request->getValue('meta_description', '');

            $predefinedParams = $request->getValue('predefinedParams');
            $validator = new Validator(array(
                'name' => trim($name),
                'alias' => $alias,
                'provider' => isset($predefinedParams['provider']) ? $predefinedParams['provider'] : ''
            ));

            $validator->addRule(new NotEmptyString(), 'name', LangAdmin::get('Name_cannot_be_empty'));
            if (in_array('Seo2', General::$enabledFeatures) && $alias != '') {
                $validator->addAliasStringValidator('alias', LangAdmin::get('Alias_is_invalid'));
            }
            if ($predefinedParams['preDefineMode'] != 'virtual') {
                $validator->addRule(new NotEmptyString(), 'provider', ''.LangAdmin::get('Provider_not_selected').'');
            }
            if (! $validator->validate()) {
                $this->respondAjaxError($validator->getErrors());
            }

            $language = $this->getCategoriesActiveLang();
            $xml = $this->generateCategoryXML($request);
            $this->categoriesNewProvider->initAddCategoryInfo($language, $language, $xml);
            $this->categoriesNewProvider->doRequests();
            $createdData = array(
                'newId' => $this->categoriesNewProvider->getAnswerAddCategoryInfo()->GetOtapiCategory()->GetId(),
                'isParent' => $this->categoriesNewProvider->getAnswerAddCategoryInfo()->GetOtapiCategory()->IsParent()
            );

            if ($seoText) {
                $this->categoriesProvider->setSeoText($createdData['newId'], $seoText, $language);
            }
            $aliasToSave = '';
            if (in_array('Seo2', General::$enabledFeatures)) {
                $aliasToSave = $alias != '' ? $alias : TextHelper::translitСonverter(trim($name));
                $this->categoriesProvider->setCategoryAlias($createdData['newId'], $aliasToSave);
                $data = array(
                    'cid' => $createdData['newId'],
                    'seo_title' => $metaTitle,
                    'meta_keywords' => $metaKeywords,
                    'meta_description' => $metaDescription,
                    'meta_title'=> $metaPagetitle,
                    'language' => $language
                );
                $this->categoriesProvider->setCategorySEO($data);
                Cacher::rRmDir(CFG_APP_ROOT . '/cache/menushortnew');
            }
            $position = 0;
            $createdData['positions'] = $this->getCategoryPositions($request, $createdData['newId'], $position);
            $createdData['position'] = $position;

        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        catch (DBException  $e) {
            $message = $e->getMessage();
            if (strstr($message, 'Duplicate entry')) {
                $this->respondAjaxError(LangAdmin::get('Duplicate_category_alias'));
            } else {
                $this->respondAjaxError($e->getMessage());
            }
        }

        $this->sendAjaxResponse(array(
            'newId' => $createdData['newId'],
            'isParent' => $createdData['isParent'],
            'aliasToSave' => $aliasToSave,
        	'positions' => $createdData['positions'],
        	'position' => $createdData['position']
        ));
    }

    private function getCategoryPositions($request, $id, &$pos)
    {
    	$positions = array();
    	try {
    		$parentId = $request->getValue('parentId');
    		$sid = Session::get('sid');
    		$categories = $this->categoriesProvider->GetEditableCategorySubcategories($sid, $parentId, 'true');
    		// apply filter
    		$language = $this->getCategoriesActiveLang();
    		$categories = $this->applyFilters($categories);
    		foreach ($categories as $key => $category) {
    			$positions[$category['id']] = $category['i'];
    			if ($id == $category['id']) {
    				$pos = $category['i'];
    			}
    		}
    	}
    	catch (Exception $e) {

    	}
    	return $positions;
    }

    public function updateCategoryAction($request)
    {
        try {
            $sid = Session::get('sid');

            $newName = (string) $request->getValue('newName');
            $categoryId =  $request->getValue('categoryId');

            $alias = $request->getValue('alias','');
            $seoText = $request->getValue('seoText','');

            $metaPagetitle = $request->getValue('meta_pagetitle', '');
            $metaTitle = $request->getValue('meta_title', '');
            $metaKeywords = $request->getValue('meta_keywords', '');
            $metaDescription = $request->getValue('meta_description', '');

            $predefinedParams = $request->getValue('predefinedParams');
            $validator = new Validator(array(
                'name' => trim($newName),
                'categoryId' => $categoryId,
                'alias' => $alias,
                'provider' => isset($predefinedParams['provider']) ? $predefinedParams['provider'] : ''
            ));
            $validator->addRule(new NotEmptyString(), 'name', LangAdmin::get('Name_cannot_be_empty'));
            $validator->addRule(new NotEmptyString(), 'categoryId', LangAdmin::get('Category_id_cannot_be_empty'));
            if ($predefinedParams['preDefineMode'] != 'virtual') {
                $validator->addRule(new NotEmptyString(), 'provider', ''.LangAdmin::get('Provider_not_selected').'');
            }
            if (in_array('Seo2', General::$enabledFeatures) && $alias != '') {
                $validator->addAliasStringValidator('alias', LangAdmin::get('Alias_is_invalid'));
            }

            if (! $validator->validate()) {
                $this->respondAjaxError($validator->getErrors());
            }

            $language = $this->getCategoriesActiveLang();

            //update category name
            $xml = $this->generateCategoryXML($request);
            $this->categoriesNewProvider->initEditCategoryInfo($language, $categoryId, $xml);
            $this->categoriesNewProvider->doRequests();
            $result = $this->categoriesNewProvider->getAnswerEditCategoryInfo()->GetOtapiCategory()->GetId();
            $isParent = $this->categoriesNewProvider->getAnswerEditCategoryInfo()->GetOtapiCategory()->IsParent();

            //update category text
            if (!$seoText) {
                $seoText = '';
            }
            $this->categoriesProvider->setSeoText($categoryId, $seoText, $language);
            $aliasToSave = '';
            if (in_array('Seo2', General::$enabledFeatures)) {
                $aliasToSave = $alias != '' ? $alias : TextHelper::translitСonverter(trim($newName));
                $this->categoriesProvider->setCategoryAlias($categoryId, $aliasToSave);
                $data = array(
                   'cid' => $categoryId,
                    'seo_title' => $metaTitle,
                    'meta_keywords' => $metaKeywords,
                    'meta_description' => $metaDescription,
                    'meta_title'=> $metaPagetitle,
                    'language' => $language
                    );

                $this->categoriesProvider->setCategorySEO($data);
                Cacher::rRmDir(CFG_APP_ROOT . '/cache/menushortnew');
            }

        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        catch (DBException  $e) {
            $message = $e->getMessage();
            if (strstr($message, 'Duplicate entry')) {
                $this->respondAjaxError(LangAdmin::get('Duplicate_category_alias'));
            } else {
                $this->respondAjaxError($e->getMessage());
            }
        }
        $this->sendAjaxResponse(array(
            'isParent' => $isParent
        ));
    }

    private function generateCategoryXML($request)
    {
        $xml = new SimpleXMLElement('<EditableCategoryInfo></EditableCategoryInfo>');

        $xml->addChild('CategoryName', trim($request->valueExists('newName') ? $request->getValue('newName') : $request->getValue('name')));
        if (($request->valueExists('parentId')) && ($request->getValue('parentId') != '')) {
            $xml->addChild('ParentId', $request->getValue('parentId'));
        }
        if (($request->valueExists('approxweight')) && ($request->getValue('approxweight') != '')) {
            $approxweight = $request->getValue('approxweight');
            $approxweight = str_replace(",", ".", $approxweight);
            $xml->addChild('ApproxWeight', $approxweight);
        } elseif (($request->valueExists('approxweight')) && ($request->getValue('approxweight') == '')) {
            $xml->addChild('ResetApproxWeight', true);
        }
        $predefinedParams = $request->getValue('predefinedParams');
        if ($predefinedParams['preDefineMode'] == 'virtual') {
            $xml->addChild('ResetSearchParameters', true);
            $xml->addChild('ExternalId', '');
        }
        if ($predefinedParams['preDefineMode'] == 'category') {
            $xml->addChild('ResetSearchParameters', true);
            $xml->addChild('ExternalId', $predefinedParams['category']['id']);
        }
        if ($request->valueExists('iconimageurl')) {
            $xml->addChild('IconImageUrl', $request->getValue('iconimageurl'));
        }
        if ($predefinedParams['preDefineMode'] == 'search') {
            if (! empty($predefinedParams['searchUrl'])) {
                $child = $xml->addChild('SearchUrl');
                $child->value = $predefinedParams['searchUrl'];
            }
            $el = $xml->addChild('SearchParameters');
            $el->addChild('Provider', $predefinedParams['provider']);
            if (! empty($predefinedParams['searchMethod'])) {
                $el->addChild('SearchMethod', $predefinedParams['searchMethod']);
            }
            if (! empty($predefinedParams['category'])) {
                $el->addChild('CategoryId', $predefinedParams['category']['id']);
            }
            if (! empty($predefinedParams['vendor'])) {
                $el->addChild('VendorId', $predefinedParams['vendor']);
            }
            if (! empty($predefinedParams['region'])) {
                $el->addChild('VendorAreaId', $predefinedParams['region']['RegionId']);
            }
            if (! empty($predefinedParams['searchWord'])) {
                $el->addChild('ItemTitle', $predefinedParams['searchWord']);
            }
            if (! empty($predefinedParams['languageOfQuery'])) {
                $el->addChild('LanguageOfQuery', $predefinedParams['languageOfQuery']);
            }
            if (! empty($predefinedParams['minPrice'])) {
                $el->addChild('MinPrice', $predefinedParams['minPrice']);
            }
            if (! empty($predefinedParams['maxPrice'])) {
                $el->addChild('MaxPrice', $predefinedParams['maxPrice']);
            }
            if (! empty($predefinedParams['brand'])) {
                $el->addChild('BrandId', $predefinedParams['brand']);
            }
            if (! empty($predefinedParams['stuffStatus'])) {
                $el->addChild('StuffStatus', $predefinedParams['stuffStatus']);
            }
            if (! empty($predefinedParams['availableSorts'])) {
                $el->addChild('OrderBy', $predefinedParams['availableSorts']);
            }
            if (! empty($predefinedParams['Configurators'])) {
                $configs = $el->addChild('Configurators');
                foreach($predefinedParams['Configurators'] as $item) {
                    $configOne = $configs->addChild('Configurator');
                    $configOne->addAttribute('Pid', $item['pid']);
                    $configOne->addAttribute('Vid', $item['vid']);
                }
            }
            if ((! empty($predefinedParams['featureDiscount'])) || (! empty($predefinedParams['featureAuction']))) {
                $features = $el->addChild('Features');
                if (! empty($predefinedParams['featureDiscount'])) {
                    $el = $features->addChild('Feature', $predefinedParams['featureDiscount']);
                    $el->addAttribute('Name', 'Discount');
                }
                if (! empty($predefinedParams['featureAuction'])) {
                    $el = $features->addChild('Feature', $predefinedParams['featureAuction']);
                    $el->addAttribute('Name', 'Auction');
                }
            }
        }
        if ($request->valueExists('categoryIconClass')) {
            $xml->addChild('MetaData')
                ->addChild('Item', $request->getValue('categoryIconClass'))
                ->addAttribute('Name', 'CategoryIconClass');
        }
        return $xml->asXML();
    }


    /**
     * @param RequestWrapper $request
     */
    public function removeCategoryAction($request)
    {
        try {
            $id = $request->getValue('id');
            $this->categoriesProvider->RemoveCategory(Session::get('sid'), $id);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function visibleCategoryAction($request)
    {
        try {
            $categoryId = $request->getValue('categoryId');
            $sessionId = Session::get('sid');
            if ($request->getValue('visible') == 'false') {
                $categorySettings = $categoryId . '-0';
            } else {
                $categorySettings = $categoryId . '-1';
            }
            $data = $this->categoriesProvider->EditCategoriesVisible($categorySettings, $sessionId);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function orderCategoryAction($request)
    {
        try {
            $categoryId = $request->getValue('categoryId');
            $sessionId = Session::get('sid');
            $i = $request->getValue('i');

            $data = $this->categoriesProvider->EditOrderOfCategory($i, $categoryId, $sessionId);

        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    public function exportTxtAction($request)
    {
        try {
            //2014_02_12
            $date = new DateTime();
            $filename = 'categories_'.$date->format('Y_m_d').'.txt';
            $sessionId = Session::get('sid');
            $data = $this->categoriesProvider->ExportStructureByLanguage($sessionId);
            if ($data) {
                header('Content-Type: text/plain; charset:utf-8;');
                header('Content-Disposition: attachment; filename="'.$filename.'"');
                echo base64_decode($data);
                echo "\r\n";
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
    }

    private function getExportXMLData($request)
    {
        $result = array();

        $date = new DateTime();
        $filename = 'categories_'.$date->format('Y_m_d').'.xml';
        $sessionId = Session::get('sid');
        $categoryId = $request->getValue('categoryId');
        if (! $categoryId) {
            $data = $this->categoriesProvider->ExportCatalog($sessionId);
        } else {
            $data = $this->categoriesProvider->ExportSubCatalogTree($sessionId, $categoryId);
            $filename = 'category_' . $categoryId . '_' . $date->format('Y_m_d') . '.xml';
        }

        if (in_array('Seo2', General::$enabledFeatures)) {
            $metaLangsData = $this->categoriesProvider->getLangsCategorySEO();
            $seoLangsData = $this->categoriesProvider->getLangsSeoText();
            $seoAliasesData = $this->categoriesProvider->getCategoriesAliases();

            if (! empty($data->Content) && ! empty($data->Content->Category)) {
                foreach ($data->Content->Category as $categoryData) {
                    $categoryData = $this->exportXMLLoop($categoryData, $metaLangsData, $seoLangsData, $seoAliasesData);
                }
            }
        }
        $content = $data->Content->asXML();
        $content = str_replace('Content', 'CatalogPackage', $content);

        $result['filename'] = $filename;
        $result['content'] = $content;

        return $result;
    }

    public function fileAction($request)
    {
        $path =  CFG_APP_ROOT.'/cache/';
        if ($request->getValue('save')) {
            try {
                $file = $this->getExportXMLData($request);
                file_put_contents($path . $file['filename'], $file['content']);
            } catch (ServiceException $e) {
                $this->respondAjaxError($e->getMessage());
            }
            $this->sendAjaxResponse(array('link' => $file['filename']));
        } elseif ($request->getValue('download')) {
            $filename = $request->getValue('download');
            $content = file_get_contents($path . $filename);
            if ($content) {
                header('Content-type: text/xml; charset=utf8');
                header('Content-Disposition: attachment; filename="'.$filename.'"');
                echo $content;
            }
        }
    }

    public function exportXmlAction($request)
    {
        try {
            $data = $this->getExportXMLData($request);
            $content = $data['content'];
            $filename = $data['filename'];
            if ($content) {
                header('Content-type: text/xml; charset=utf8');
                header('Content-Disposition: attachment; filename="'.$filename.'"');
                echo $content;
            }
        } catch (Exception $e) {
            print $e->getMessage();
            exit;
        }
    }

    private function exportXMLLoop($categoryData, $metaLangsData, $seoLangsData, $seoAliasesData)
    {
        $seoData = $categoryData->addChild('SeoInfo');
        $catId = (string)$categoryData->InternalId;
        if (! empty($seoAliasesData[$catId])) $seoData->addChild('Alias', $this->escape($seoAliasesData[$catId]));

        if (! empty($metaLangsData[$catId])) foreach ($metaLangsData[$catId] as $lang => $meta) {
            $metaTitle = $seoData->addChild('MetaTitles');
            $metaKeywords = $seoData->addChild('MetaKeywords');
            $metaDescriptions = $seoData->addChild('MetaDescriptions');
            $metaSeoTitle = $seoData->addChild('MetaSeoTitles');


            $el = $metaTitle->addChild('MetaTitle', $this->escape($meta['pagetitle']));
            $el->addAttribute('Language', $lang);

            $el = $metaKeywords->addChild('MetaKeywords', $this->escape($meta['seo_keywords']));
            $el->addAttribute('Language', $lang);

            $el = $metaDescriptions->addChild('MetaDescription', $this->escape($meta['seo_description']));
            $el->addAttribute('Language', $lang);

            $el = $metaSeoTitle->addChild('MetaSeoTitle', $this->escape($meta['seo_title']));
            $el->addAttribute('Language', $lang);
        }
        if (! empty($seoLangsData[$catId])) foreach ($seoLangsData[$catId] as $lang => $seo) {
            $descriptions = $seoData->addChild('Descriptions');
            $el = $descriptions->addChild('Description', $this->escape($seo['text']));
            $el->addAttribute('Language', $lang);
        }
        if ($categoryData->Children->Category) foreach ($categoryData->Children->Category as $child) {
            $child = $this->exportXMLLoop($child, $metaLangsData, $seoLangsData, $seoAliasesData);
        } else foreach ($categoryData->Children as $child) {
            $child = $this->exportXMLLoop($child, $metaLangsData, $seoLangsData, $seoAliasesData);
        }
        return $categoryData;
    }

    public function importAction($request)
    {
        try {
        	$categoryId = $request->post('categoryId');
        	$errors = array();
            $uploadResult = json_decode($this->uploadFile());
            if ($uploadResult && $uploadResult->uploaded_file[0]->size > 0) {
                $sessionId = Session::get('sid');
                $path = CFG_APP_ROOT . '/uploaded/categories/' . $uploadResult->uploaded_file[0]->name;
                $content = file_get_contents($path);
                if ($uploadResult->uploaded_file[0]->type == 'text/plain' && ! $categoryId) {
                    $sorce = base64_encode($content);
                    $data = $this->categoriesProvider->ImportStructureByLanguage($sessionId, $sorce);
                } elseif ($uploadResult->uploaded_file[0]->type == 'text/xml') {
                    $id2id = null;
                    if (!$categoryId) {
                        $this->categoriesProvider->DeleteCatalogSeo();
                        $data = $this->categoriesProvider->ImportCatalog($sessionId, $content);
                    } else {
                        $data = $this->categoriesProvider->ImportSubCatalogTree($sessionId, $categoryId, $content);
                        $id2id = array();
                        $categoriesToRemove = array();
                        foreach ($data as $k => $ids) {
                            $id2id[$ids['ImportId']] = $ids['ActualId'];
                            $categoriesToRemove[] = $ids['ActualId'];
                        }
                        $this->existingCategory = $this->getExistingCategories();
                        $this->setCategoryAlias();
                        $this->setCategorySEO();
                        $this->setCategorySEOText();
                    }
                    $catalogXml = @simplexml_load_string($content);
                    if (General::IsFeatureEnabled('Seo2') && isset($catalogXml->Category)) {
                        // Clear the seo information before importing
                        if (isset($categoriesToRemove) && !empty($categoriesToRemove)) {
                            $categoriesToRemoveFrames = array_chunk($categoriesToRemove, 100);
                            $this->categoriesProvider->DeleteCatalogSeoFrames($categoriesToRemoveFrames);
                        }
                        foreach ($catalogXml->Category as $category) {
                            $this->importXMLLoop($category, $errors, $id2id);
                        }
                    }
                }
            } else {
            	$this->respondAjaxError(LangAdmin::get('Could_not_load_file_categories') . '. ' . $uploadResult->uploaded_file[0]->error);
                /*Session::setError(
                    LangAdmin::get('Could_not_load_file_categories') . '. ' . $uploadResult->uploaded_file[0]->error
                );*/
            }
        } catch (ServiceException $e) {
            //Session::setError($e->getErrorMessage());
        	$this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse(array(
        		'result' => 'ok',
        		'errors' => $errors
        ));
        //header('Location: index.php?cmd=categories');
    }

    private function getExistingCategories()
    {
        if (is_null($this->existingCategory)) {
            $requiredTable = array('site_categories');
            $sql = 'SELECT category_id FROM site_categories';

            $query = General::getCms()->query($sql, $requiredTable);

            $result = array();
            while ($row = mysqli_fetch_assoc($query)) {
                $result[$row['category_id']] = true;
            }
            $this->existingCategory = $result;
        }

        return $this->existingCategory;
    }

    private function getExistingAliases()
    {
        if (is_null($this->existingCategoriesAliases)) {
            $requiredTable = array('site_categories');
            $sql = 'SELECT * FROM site_categories';

            $query = General::getCms()->query($sql, $requiredTable);

            $result = array();

            while ($row = mysqli_fetch_array($query)) {
                $result[$row['alias']] = true;
            }

            $this->existingCategoriesAliases = $result;
        }

        return $this->existingCategoriesAliases;
    }

    private function getExistingSeoText()
    {
        if (is_null($this->existingSeoText)) {
            $requiredTable = array('site_categories_seo_texts');
            $sql = 'SELECT concat(category_id, "_", lang_code) category_id_lang FROM site_categories_seo_texts';

            $query = General::getCms()->query($sql, $requiredTable);

            $result = array();
            while ($row = mysqli_fetch_assoc($query)) {
                $result[] = $row['category_id_lang'];
            }
            $this->existingSeoText = $result;
        }

        return $this->existingSeoText;
    }

    private function getExistingCategorySEO()
    {
        if (is_null($this->existingCategorySEO)) {
            $requiredTable = array('site_pages_langs_data');
            $sql = 'SELECT concat(p, "_", lang_id) category_id_lang FROM `site_pages_langs_data`';

            $query = General::getCms()->query($sql, $requiredTable);

            $result = array();
            while ($row = mysqli_fetch_assoc($query)) {
                $result[] = $row['category_id_lang'];
            }
            $this->existingCategorySEO = $result;
        }

        return $this->existingCategorySEO;
    }

    private function getExistingLangsId()
    {
        if (empty($this->existingLangsId)) {
            $requiredTable = array('site_langs');
            $sql = 'SELECT id, lang_code FROM site_langs';

            $query = General::getCms()->query($sql, $requiredTable);

            $result = array();
            while ($row = mysqli_fetch_assoc($query)) {
                $result[$row['lang_code']] = $row['id'];
            }
            $this->existingLangsId = $result;
        }

        return $this->existingLangsId;
    }

    public function checkCategoryDuplicate($categoryId) {
        return array_key_exists($categoryId, $this->existingCategory);
    }

    public function checkCategorySeoDuplicate($categoryIdLang) {
        return in_array($categoryIdLang, $this->existingCategorySEO);
    }

    public function checkCategorySeoTextDuplicate($categoryIdLang) {
        return in_array($categoryIdLang, $this->existingSeoText);
    }

    public function setCategorySEOText()
    {
        $start = 0;

        while($start <= count($this->insertSEOTextArray)) {
            $sqlInsert = 'INSERT INTO `site_categories_seo_texts` (`category_id`, `text`, `lang_code`) VALUES';
            $insertCategoriesSEOTextArrayChunk = array_slice($this->insertSEOTextArray, $start, 1000);
            $total = count($insertCategoriesSEOTextArrayChunk);
            $sqlValue = null;

            foreach ($insertCategoriesSEOTextArrayChunk as $key => $value) {
                if (!empty($value['text'])) {
                    $isset = $this->checkCategorySeoTextDuplicate($key);
                    $text = $this->cms->escape($value['text']);
                    $categoryId = $value['categoryId'];
                    $lang = $value['lang'];

                    if ($isset) {
                        $updateData = '';

                        if (!empty($text)) {
                            $updateData .= ' `text` = "'
                                . $text . '"';
                        }

                        if (!empty($updateData)) {
                            $sqlUpdate
                                = 'UPDATE `site_categories_seo_texts` SET `category_id` = "'
                                . $categoryId . '", ' . $updateData
                                . ' WHERE `category_id` = "'
                                . $categoryId . '" AND `lang_code` = "'
                                . $lang . '"';
                            $this->cms->query($sqlUpdate);
                        }
                    } else {
                        if ($sqlValue !== null) {
                            $sqlValue .= ',';
                        }
                        $sqlValue .= '("' . $categoryId . '", "'
                            . $text . '", "'
                            . $lang . '")';
                        $sqlInsert .= $sqlValue;
                    }
                }
            }
            if ($sqlValue !== null) {
                $this->cms->query($sqlInsert);
            }
            if ($total === 0) { //если кончились элементы в массиве, то прекращаем цикл
                break;
            }
            $start += $total;
        }
    }



    public function setCategorySEO()
    {
        $sqlValue = null;
        $start = 0;


        while($start <= count($this->insertSEOArray)) {
            $sqlInsert = 'INSERT INTO `site_pages_langs_data` (`p`, `seo_title`, `pagetitle`, `seo_keywords`, `seo_description`, `type`, `lang_id` ) VALUES';
            $insertCategoriesSEOArrayChunk = array_slice($this->insertSEOArray, $start, 1000);
            $total = count($insertCategoriesSEOArrayChunk);
            $sqlValue = null;

            foreach ($insertCategoriesSEOArrayChunk as $key => $value) {
                $isset = $this->checkCategorySeoDuplicate($key);
                $categoryId = $value['categoryId'];
                $seoTitle = $this->cms->escape($value['seo_title']);
                $metaTitle = $this->cms->escape($value['meta_title']);
                $metaKeywords = $this->cms->escape($value['meta_keywords']);
                $metaDescription = $this->cms->escape($value['meta_description']);
                $lang = $value['lang'];
                if ($isset) {
                    $updateData = '';

                    if (!empty($seoTitle)) $updateData .= ', `seo_title` = "' . $seoTitle . '"';
                    if (!empty($metaTitle)) $updateData .= ', `pagetitle` = "' . $metaTitle . '"';
                    if (!empty($metaKeywords)) $updateData .= ', `seo_keywords` = "' . $metaKeywords . '"';
                    if (!empty($metaDescription)) $updateData .= ', `seo_description` = "' . $metaDescription . '"';
                    if (!empty($updateData)) {
                        $sqlUpdate = 'UPDATE `site_pages_langs_data` SET `p` = "' . $categoryId . '" '.$updateData.' WHERE `p` = "' . $categoryId . '" AND `lang_id` = "' . $lang . '"';
                        $this->cms->query($sqlUpdate);
                    }
                } else {
                    if ($sqlValue !== null) {
                        $sqlValue .= ',';
                    }
                    $sqlValue = '("' . $categoryId . '", "'
                        . $seoTitle . '", "'
                        . $metaTitle.'", "'
                        . $metaKeywords.'", "'
                        . $metaDescription.'", "category", "'
                        . $lang.'")';
                    $sqlInsert .= $sqlValue;
                }
            }
            if ($sqlValue !== null) {
                $this->cms->query($sqlInsert);
            }
            if ($total === 0) {
                break;
            }
            $start += $total;
        }
    }

    public function setCategoryAlias()
    {
            $this->cms->checkTable('site_categories');

            $start = 0; // начальная позиция для array_slice

            while($start <= count($this->insertAliasesArray)) { //крутим массив пока стартовая позиция меньше размера массива, то есть пока есть элементы
                $sqlInsert = 'INSERT INTO `site_categories` (`category_id`, `alias`, `lang_id`) VALUES';
                $sqlValue = null;

                $insertAliasesArrayChunk = array_slice($this->insertAliasesArray, $start, 1000);
                $total = count($insertAliasesArrayChunk);

                foreach ($insertAliasesArrayChunk as $value) {
                    $alias = $this->escape($value['alias']);
                    $categoryId = $value['categoryId'];
                    $categoryDuplicateFlag = $this->checkCategoryDuplicate($value['categoryId']);

                    if ($categoryDuplicateFlag === false) { // если категории нет в таблице алиасов, то собираем запрос на insert

                        if ($sqlValue !== null) { // если ещё не было элементов для добавления, то вставляем кусок кода без запятой в начале
                            $sqlValue = ',';
                        }
                        $sqlValue = '("' . $categoryId . '", "' . $alias . '", "0")';
                        $sqlInsert .= $sqlValue;
                    } else { // иначе обновляем алиас у уже существующей категории
                        $sqlUpdate = 'UPDATE `site_categories` SET `alias` = "'.$alias.'" WHERE `category_id` = "'.$categoryId.'"';
                        $this->cms->query($sqlUpdate);
                    }
                }
                if ($total === 0) { //если кончились элементы в массиве, то прекращаем цикл
                    break;
                }
                if ($sqlValue !== null) {
                    $this->cms->query($sqlInsert);
                }

                $start += $total;
            }
    }

    private function importXMLLoop($category, &$errors, $id2id = null)
    {
        if ($category->Children->Category) foreach ($category->Children->Category as $child) {
            $child = $this->importXMLLoop($child, $errors, $id2id);
        } else foreach ($category->Children as $child) {
            $child = $this->importXMLLoop($child, $errors, $id2id);
        }

        $categoryId = (string)$category->InternalId;

        if ($id2id) { // если импорт частичный
            if (isset($id2id[$categoryId])) {
                // если сервис вернул importId = $category->InternalId
                // заменить категорию на actualId
                $categoryId = $id2id[$categoryId];
            } else {
                return;
            }
        }
        //=============== ALIAS ===================
        if (! empty($category->SeoInfo->Alias)) {
            try {
                $this->getExistingAliases();
                $alias = (string)$category->SeoInfo->Alias;
                $this->prepareArrayAliases($categoryId, $alias);
            }
            catch (Exception  $e) {
                $errors[] = LangAdmin::get('Duplicate_imported_category_alias', array('category' => $categoryId, 'categoryAlias' => (string)$category->SeoInfo->Alias));
            }
        }

        //=============== CATEGORY META ===================
        $seoUpdateData = array();
        if (! empty($category->SeoInfo->MetaTitles)) foreach ($category->SeoInfo->MetaTitles->MetaTitle as $mTitle) {
            $b = (string)$mTitle->attributes()->Language;
            if (empty($seoUpdateData[$b])) $seoUpdateData[$b] = array();
            $seoUpdateData[$b]['cid'] = $categoryId;
            $seoUpdateData[$b]['language'] = $b;
            $seoUpdateData[$b]['meta_title'] = (string)$mTitle;
        }
        if (! empty($category->SeoInfo->MetaKeywords)) foreach ($category->SeoInfo->MetaKeywords->MetaKeywords as $mKeys) {
            $b = (string)$mKeys->attributes()->Language;
            if (empty($seoUpdateData[$b])) $seoUpdateData[$b] = array();
            $seoUpdateData[$b]['cid'] = $categoryId;
            $seoUpdateData[$b]['language'] = $b;
            $seoUpdateData[$b]['meta_keywords'] = (string)$mKeys;
        }
        if (! empty($category->SeoInfo->MetaDescriptions)) foreach ($category->SeoInfo->MetaDescriptions->MetaDescription as $mDesc) {
            $b = (string)$mDesc->attributes()->Language;
            if (empty($seoUpdateData[$b])) $seoUpdateData[$b] = array();
            $seoUpdateData[$b]['cid'] = $categoryId;
            $seoUpdateData[$b]['language'] = $b;
            $seoUpdateData[$b]['meta_description'] = (string)$mDesc;
        }
        if (! empty($category->SeoInfo->MetaSeoTitles)) foreach ($category->SeoInfo->MetaSeoTitles->MetaSeoTitle as $mSeoTitles) {
            $b = (string)$mSeoTitles->attributes()->Language;
            if (empty($seoUpdateData[$b])) $seoUpdateData[$b] = array();
            $seoUpdateData[$b]['cid'] = $categoryId;
            $seoUpdateData[$b]['language'] = $b;
            $seoUpdateData[$b]['seo_title'] = (string)$mSeoTitles;
        }
        if (! empty($seoUpdateData)) foreach($seoUpdateData as $data) {
            $existingLangsId = $this->getExistingLangsId();
            if (isset($existingLangsId[$data['language']]) && !empty($existingLangsId[$data['language']])) {
                $data['language'] = $existingLangsId[$data['language']];
            } else {
                $data['language'] = $existingLangsId['ru'];
            }
            $this->getExistingCategorySEO();
            $this->insertSEOArray[$categoryId."_". $data['language']] = array(
                "categoryId" => $categoryId,
                "lang" => $data['language'],
                "meta_title" => $data['meta_title'],
                "meta_description" => $data['meta_description'],
                "seo_title" => $data['seo_title']
            );
        }

        //=============== CATEGORY SEO TEXTS ===================
        $seoTextsUpdateData = array();
        if (! empty($category->SeoInfo->Descriptions)) foreach ($category->SeoInfo->Descriptions->Description as $description) {
            $seoTextsUpdateData[] = array(
                'id' => $categoryId,
                'text' => (string)$description,
                'language' => (string)$description->attributes()->Language
            );
        }
        if (! empty($seoTextsUpdateData)) foreach($seoTextsUpdateData as $data) {
            $this->getExistingSeoText();
            $this->insertSEOTextArray[$data['id'].'_'.$data['language']] = array(
                'categoryId' => $data['id'],
                'text' => $data['text'],
                'lang' => $this->cms->escape($data['language'])
            );
        }
    }

    protected function prepareArrayAliases($cid, $alias)
    {
        if (!isset($this->existingCategoriesAliases[$alias])) {
            $this->insertAliasesArray[] = array('categoryId' => $cid, 'alias' => $alias);
            return $alias;
        }

        if (preg_match('/otc-\d+$/', $alias)) {
            $newAlias = preg_replace('/otc-\d+$/', $cid, $alias);
        } else {
            $newAlias = $alias . '-' . $cid;
        }

        $newAlias =  $this->getNewAlias($alias, $newAlias);
        $this->insertAliasesArray[] = array('categoryId' => $cid, 'alias' => $newAlias);

        return $newAlias;

    }

    private function getNewAlias($alias, $newAlias, $step = 2)
    {
        if (isset($this->existingCategoriesAliases[$newAlias])) {
            $newAlias = $alias . '-' . $step++;
            return $this->getNewAlias($alias, $newAlias, $step);
        }

        return $newAlias;
    }

    private function uploadFile()
    {
        ob_start();
        new UploadHandler(array(
            'param_name' => 'uploaded_file',
            'accept_file_types' => '/\.(txt|xml)$/i'
        ), true, null, '/uploaded/categories/');
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

    public function getCategoryDataAction($request)
    {
        ini_set('memory_limit', '1024M');

        $seoText = '';
        try {
            $sessionId = Session::get('sid');
            $categoryId = $request->getValue('categoryId');
            $externalId = $request->getValue('externalId');
            $regionId = $request->getValue('regionId');
            /*
            Возвращает
            $externalCategory['category'] = информация о категории с сервисов
            $externalCategory['categoryWasDeleted'] = флаг была ли категории удалена с провайдра;
            */
            $externalCategory = $this->getCategoryDataFromServices($externalId);

            if (! empty($regionId)) {
                $regionName = $this->categoriesProvider->GetRegionName($regionId);
            } else {
                $regionName = false;
            }
            $language = $this->getCategoriesActiveLang();
            $seoText = $this->categoriesProvider->getSeoText($categoryId, $language);
            if ($externalCategory['category'] && ($externalCategory['category']['ProviderType'] != 'Warehouse')) {
                $searchprops = $this->categoriesProvider->BatchSearchItemsFrame($categoryId, $externalCategory['category']['ProviderType']);
                $totalCount = $searchprops['Items']['Items']['totalcount'];
                $searchprops = $searchprops['SearchProperties'];
            } else {
                $totalCount = false;
                $searchprops = array();
            }
            $searchprops = $this->prepareSearchFilters($searchprops);
            $searchFilters = $this->generateSearchFilterEditor($searchprops);
            $сontentsProvider = new ContentsProvider($this->cms, $this->getOtapilib());

        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'seoText' => $сontentsProvider->tinyMceEntityFix($seoText),
            'filters' => $searchFilters,
            'totalCount' => $totalCount,
            'externalCategory' => $externalCategory['category'],
            'categoryWasDeleted' => $externalCategory['categoryWasDeleted'],
            'regionName' => $regionName,
            'Configurators' => $searchprops
        ));
    }

    private function generateSearchFilterEditor($searchprops)
    {
        $this->_template = 'searchFilters';

        $this->tpl->assign('searchprops', $searchprops);
        return $this->fetchTemplateWithoutHeaderAndFooter(false);
    }

    private function prepareSearchFilters($searchprops)
    {
        $searchPropsEdited = array();
        foreach ($searchprops as $value) {

            $searchPropsEdited[$value['Id']] = array(
                'name' => $value['Name'],
                'values' => array(),
            );
            $properties = $value['Values'];
            foreach ($properties as $param) {
                $prop_id = $param['Id'];
                $value_id = $param['Name'];
                if (!empty($prop_id)) $searchPropsEdited[$value['Id']]['values'][$prop_id] = array(
                    'id' => $prop_id,
                    'name' => $value_id,
                );
            }
        }
        return $searchPropsEdited;
    }

    public function saveFilterAction($request)
    {
        try {
            $sessionId = Session::get('sid');

            $langId = $this->getCategoriesActiveLang();
            $categoryId = $request->get('categoryId');
            $name = $request->getValue('name');
            $value = $request->getValue('value');

            $this->categoriesProvider->updateSearchFilter($categoryId, $name, $value, $sessionId, $langId);

        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }


    public function getHintAction($request)
    {
        try {
            $sessionId = Session::get('sid');
            $name = $request->getValue('name');

            $categories = $this->categoriesProvider->FindHintCategoryInfoList($name);
            $hints = array();

            foreach ($categories as $category) {
                $path = '';
                if (isset($category['path'])) {
                    $paths = array();
                    foreach ($category['path'] as $pitem) {
                        $paths[] = $pitem['name'];
                    }
                    $path = implode(' > ', $paths)  . ' > ' . $category['name'];
                }
                else {
                    $path = $category['name'];
                }

                $hint = array();
                $hint['id'] = $category['id'];
                $hint['label'] = $path;

                $hints[] = $hint;
            }
            $this->sendAjaxResponse($hints);
        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);
        }
    }

    private function copyCategories($parentId, $targetId)
    {
        try {
            $sessionId = Session::get('sid');
            $language = $this->getCategoriesActiveLang();

            $categories = $this->categoriesProvider->GetEditableCategorySubcategories($sessionId, $parentId, 'true');
            if (is_array($categories)) {
                foreach ($categories as $key => &$category) {
                    try {
                        $xml = '<?xml version="1.0"?><EditableCategoryInfo>';
                        $xml .= '<SearchParameters>' . $this->getXmlFromCategoryInfoArray($category['SearchParameters']) . '</SearchParameters>';
                        $xml .= '<ParentId>' . $targetId . '</ParentId>';
                        $xml .= '<CategoryName>' . $category['name'] . '</CategoryName>';
                        $xml .= '</EditableCategoryInfo>';

                        $this->categoriesNewProvider->initAddCategoryInfo($language, $language, $xml);
                        OTAPILib2::makeRequests();
                        $newId = $this->categoriesNewProvider->getAnswerAddCategoryInfo()->GetOtapiCategory()->GetId();
                    } catch (ServiceException $e) {
                    }
                    if ($newId && $category['IsParent'] == 'true') {
                        $this->copyCategories($category['Id'], $newId);
                    }
                }
            }
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }
    }

    public function copyPasteAction($request){
        try {
            $sessionId = Session::get('sid');
            $copiedId = $request->getValue('copiedId');
            $copiedParentId = $request->getValue('parentId') ?: 0;
            $targetId = $request->getValue('targetId');
            $copiedName = $request->getValue('copiedName');
            $copiedExternalId = $request->getValue('copiedExternalId');
            $sid = Session::get('sid');

            if ($copiedName) {
                $language = $this->getCategoriesActiveLang();

                $categories = $this->categoriesProvider->GetEditableCategorySubcategories($sid, $copiedParentId, 'true');

                $categoryIds = array_map(function($a) {
                    return $a['id'];
                }, $categories);
                $categoryKey = array_search($copiedId, $categoryIds);
                $category = $categories[$categoryKey];

                $xml = '<?xml version="1.0"?><EditableCategoryInfo>';
                $xml .= '<SearchParameters>' . $this->getXmlFromCategoryInfoArray($category['SearchParameters']) . '</SearchParameters>';
                $xml .= '<ParentId>' . $targetId . '</ParentId>';
                $xml .= '<CategoryName>' . $copiedName . '</CategoryName>';
                $xml .= '</EditableCategoryInfo>';

                $this->categoriesNewProvider->initAddCategoryInfo($language, $language, $xml);
                OTAPILib2::makeRequests();
                $newId = $this->categoriesNewProvider->getAnswerAddCategoryInfo()->GetOtapiCategory()->GetId();
                if ($newId) {
                    $this->copyCategories($copiedId, $newId);
                }
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    public function getXmlFromCategoryInfoArray($arr){
        $xml = '';
        foreach ($arr as $key => $value) {
            if (preg_match('/(^[0-9])|(^[a-z]*)$/', $key) || empty($value)) continue;
            if (is_array($value)) {
                $xml .= '<' . $key . '>' . $this->getXmlFromCategoryInfoArray($value) . '</' . $key . '>';
                continue;
            }
            $xml .= '<' . $key . '>' . $value . '</' . $key . '>';
        }
        return $xml;
    }

    public function getSearchParamsFormAction($request){
        try {
            $lang = Session::getActiveAdminLang();
            $sessionId = Session::get('sid');
            $searchProvider = $request->getValue('searchProvider');
            $this->categoriesNewProvider->initGetProviderSearchMethodInfoList($lang);
            $this->categoriesNewProvider->doRequests();
            $searchMethods = $this->categoriesNewProvider->getProviderSearchMethodInfoList()->GetResult()->GetContent();

            $instanceProvider = InstanceProvider::getObject();
            $providerOptions = $instanceProvider->GetProviderInfo($lang, $searchProvider);

            $selectedSearchMethods = array();
            $selectedSearchMethodsName = array();
            foreach($searchMethods->GetItem() as $method) {
                if ($method->GetProvider() == $searchProvider) {
                    $selectedSearchMethods[] = $method;

                    $providerMethod = $method->GetProvider() . '_' . $method->GetSearchMethod();
                    $methodName = $method->GetDisplayName();
                    // если есть перевод названия поиска - берем перевод, иначе название берется с сервисов
                    if (LangAdmin::get($providerMethod.'_Flag') != $providerMethod.'_Flag') {
                        $methodName = LangAdmin::get($providerMethod.'_Flag');
                    }
                    $selectedSearchMethodsName[$method->GetSearchMethod()] = $methodName;
                }
            }

            $providerLang = (string)$providerOptions->GetLanguage();
            $usedLaguages = array(
                $providerLang => InstanceProvider::getObject()->getLanguageDescriptionByCode($providerLang, $lang, $sessionId)
            );

            $WebUI = $this->languagesProvider->GetLanguages();
            if ($WebUI->UsedLanguages->string) foreach($WebUI->UsedLanguages->string as $lang) {
                $langDescription = '';
                foreach($WebUI->Languages->NamedProperty as $langSearch){
                    if ((string)$lang == (string)$langSearch->Name){
                        $langDescription = (string)$langSearch->Description;
                        break;
                    }
                }
                $usedLaguages[(string)$lang] = $langDescription;
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->_template = 'searchParamsForm';
        $this->tpl->assign('providerCurrency', $providerOptions->GetCurrencyCode());
        $this->tpl->assign('selectedSearchMethods', $selectedSearchMethods);
        $this->tpl->assign('selectedSearchMethodsName', $selectedSearchMethodsName);
        $this->tpl->assign('usedLaguages', $usedLaguages);
        $this->sendAjaxResponse(array(
            'form' => $this->fetchTemplateWithoutHeaderAndFooter(),
            'searchMethods' => $this->prepareJSONSearchMethods($selectedSearchMethods)
        ));
    }

    public function getCategoriesByProviderAction($request){
        try {
            $categoryRoot = $request->getValue('categoryRoot');
            $canSearchRootCategory = $request->getValue('canSearchRootCategory');
            $categories = $this->categoriesProvider->GetProviderCategorySubcategories($categoryRoot);
            if ($canSearchRootCategory == 'true') {
                $data = array();
                $data[0] = array(
                    'Id' => 0,
                    'id' => 0,
                    'ExternalId' => $categoryRoot,
                    'externalid' => $categoryRoot,
                    'Name' => ''.LangAdmin::get('All_categories').' (root)',
                    'name' => ''.LangAdmin::get('All_categories').' (root)',
                    'ApproxWeight' => '',
                    'approxweight' => '',
                	'children' => $categories,
                	'Children' => $categories,
                	'IsParent' => 'true',
                	'isparent' => 'true'
                );
                $categories = $data;
            }
        } catch (ServiceException $e) {
            $categories = array();
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'categories' => $categories
        ));
    }

    public function getCategoryFiltersDataAction($request)
    {
        try {
            $sessionId = Session::get('sid');
            $categoryId = $request->getValue('categoryId');
            $configs = $request->getValue('configurators', []);

            $searchprops = $this->categoriesProvider->GetCategorySearchProperties($categoryId);

            foreach ($configs as $config) {
                if (!isset($searchprops[$config['pid']])) {
                    if (!isset($searchprops[$config['pid']])) {
                        $searchprops[$config['pid']] = array(
                            'name' => $config['name'],
                            'values' => array()
                        );
                    }
                }
                if (!isset($searchprops[$config['pid']]['values'][$config['vid']])) {
                    $searchprops[$config['pid']]['values'][$config['vid']] = array(
                        'id' => $config['vid'],
                        'name' => $config['valueName'],
                    );
                }
            }

            $searchFilters = $this->generateSearchFilterEditorByChecks($searchprops);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array('filters' => $searchFilters));
    }

    private function prepareJSONSearchMethods($searchMethods){
        $result = array();
        foreach ($searchMethods as $method) {
            $sorts = false;
            if ($method->GetAvailableSorts()) {
                $sorts = array();
                foreach ($method->GetAvailableSorts()->GetSort() as $sort) {
                    $sorts[$sort->GetOrderBy()] = $sort->GetDisplayName();
                }
            }
            $tmp = array(
                'method'            => $method->GetSearchMethod(),
                'Vendor'            => $method->Vendor(),
                'VendorLocation'    => $method->VendorLocation(),
                'Brand'             => $method->Brand(),
                'Configurators'     => $method->Configurators(),
                'PriceRange'        => $method->PriceRange(),
                'VendorRatingRange' => $method->VendorRatingRange(),
                'VolumeRange'       => $method->VolumeRange(),
                'StuffStatus'       => $method->StuffStatus(),
                'AvailableSorts'    => $sorts
            );
            foreach ($method->GetFeatures()->GetFeature() as $feature) {
                $tmp['Feature' . $feature->GetName()] = true;
            }
            $result[] = $tmp;
        }
        return json_encode($result);
    }

    private function generateSearchFilterEditorByChecks($searchprops)
    {
        $this->_template = 'searchFiltersByChecks';

        $this->tpl->assign('searchprops', $searchprops);
        return $this->fetchTemplateWithoutHeaderAndFooter(false);
    }

    private function getCategoryDataFromServices($externalId)
    {
        $externalCategory['category'] = false;
        $externalCategory['categoryWasDeleted'] = false;
        try {
            if (! empty($externalId)) {
                $externalCategory['category'] = $this->categoriesProvider->GetProviderCategory($externalId);
            }
        } catch (ServiceException $e) {
            if (($e->getErrorCode() == 'NotFound') && ($e->getSubErrorCode() == 'CategoryWithId')) {
                $externalCategory['categoryWasDeleted'] = true;
            } else {
                throw $e;
            }
        }
        return $externalCategory;
    }

    private function bindPredifenedParams($categories)
    {
        $this->categoriesNewProvider->initGetProviderSearchMethodInfoList(Session::getActiveAdminLang());
        $this->categoriesNewProvider->doRequests();
        $searchMethods = $this->categoriesNewProvider->getProviderSearchMethodInfoList()->GetResult()->GetContent();

        $newCategories = array();
        foreach ($categories as $key => $category) {
            $predifenedParams = array();
            if ($category['IsVirtual'] == 'true') {
                $predifenedParams['preDefineMode'] = 'virtual';
            } else if (empty($category['SearchParameters']['Provider'])) {
                $predifenedParams['preDefineMode'] = 'category';
                $predifenedParams['provider'] = $category['ProviderType'];
                $predifenedParams['category'] = array(
                    'name' => '',
                    'id' => $category['ExternalId']
                );
            } else {
                $predifenedParams['preDefineMode'] = 'search';
                $predifenedParams['provider'] = $category['SearchParameters']['Provider'];
                $predifenedParams['searchWord'] = $category['SearchParameters']['ItemTitle'];
                $predifenedParams['languageOfQuery'] = $category['SearchParameters']['LanguageOfQuery'];
                $predifenedParams['searchMethod'] = $category['SearchParameters']['SearchMethod'];
                foreach($searchMethods->GetItem() as $method) {
                    if ($method->GetProvider() == $category['SearchParameters']['Provider'] &&
                        $method->GetSearchMethod() == $category['SearchParameters']['SearchMethod']) {
                        $predifenedParams['searchMethodName'] = $method->GetDisplayName();
                        if (isset($category['SearchParameters']['OrderBy']) && $method->GetAvailableSorts()) foreach ($method->GetAvailableSorts()->GetSort() as $sort) {
                            if ($sort->GetOrderBy() == $category['SearchParameters']['OrderBy']) {
                                $predifenedParams['availableSorts'] = $category['SearchParameters']['OrderBy'];
                                $predifenedParams['availableSortsName'] = $sort->GetDisplayName();
                                break;
                            }
                        }
                        break;
                    }
                }
                if (!empty($category['SearchParameters']['VendorId'])){
                    $predifenedParams['vendor'] = $category['SearchParameters']['VendorId'];
                }else{
                    $predifenedParams['vendor'] = $category['SearchParameters']['VendorName'];
                }
                $predifenedParams['minPrice'] = $category['SearchParameters']['MinPrice'];
                $predifenedParams['maxPrice'] = $category['SearchParameters']['MaxPrice'];
                $predifenedParams['brand'] = $category['SearchParameters']['BrandId'];
                $predifenedParams['stuffStatus'] = $category['SearchParameters']['StuffStatus'];
                $predifenedParams['featureDiscount'] = isset($category['SearchParameters']['Features']['Discount']) ? $category['SearchParameters']['Features']['Discount'] : '';
                $predifenedParams['featureAuction'] = isset($category['SearchParameters']['Features']['Auction']) ? $category['SearchParameters']['Features']['Auction'] : '';
                if (! empty($category['SearchParameters']['Configurators'])) {
                    foreach ($category['SearchParameters']['Configurators'] as $conf) {
                        $predifenedParams['Configurators'][] = array(
                            'pid' => (string)$conf['Pid'],
                            'vid' => (string)$conf['Vid'],
                            'name' => '',
                            'valueName' => ''
                        );
                    }
                }
                if (! empty($category['SearchParameters']['VendorAreaId'])) {
                    $predifenedParams['region'] = array(
                        'RegionId' => $category['SearchParameters']['VendorAreaId'],
                        'name' => ''
                    );
                }
                if (! empty($category['SearchParameters']['CategoryId'])) {
                    $predifenedParams['category'] = array(
                        'name' => '',
                        'id' => $category['SearchParameters']['CategoryId']
                    );
                }
            }
            $newCategories[] = array_merge($category, array('predifenedParams' => json_encode($predifenedParams)));
        }
        return $newCategories;
    }

    private function getCategoriesActiveLang()
    {
        $language = Session::get('active_lang_categories');
        if (empty($language)) {
            $language = Session::getActiveLang();
            Session::set('active_lang_categories', $language);
        }
        return $language;
    }

    public function uploadImageAction($request)
    {
        try {
            if ($request->getValue('delete_image')) {
                $logoUrl = '';
            } else {
                if (empty($_FILES['input_image']['tmp_name'])) {
                    $this->respondAjaxError('No image was selected to upload.');
                }
                $uploadResult = $this->uploadImage();
                if (isset($uploadResult['input_image'][0])) {
                    if (isset($uploadResult['input_image'][0]->url)) {
                        $logoUrl = $uploadResult['input_image'][0]->url;
                    } else if (isset($uploadResult['input_image'][0]->error)) {
                        $this->respondAjaxError($uploadResult['input_image'][0]->error);
                    }
                } else {
                    $this->respondAjaxError('Unknown error occured while uploading image. Try again.');
                }
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'url' => $logoUrl,
        ));
    }

    private function uploadImage()
    {
        $uploader = new UploadHandler(array(
            'param_name' => 'input_image',
            'image_versions' => array(
                '' => array(
                    'max_width' => null,
                    'max_height' => null,
                    'jpeg_quality' => 90
                ),
            ),
        ), false, null, '/uploaded/category/');
        return $uploader->post(false);
    }

    public function moveCategoriesAction($request)
    {
        $sessionId = Session::get('sid');
        $language = $this->getCategoriesActiveLang();
        $categories = $request->getValue('categories');
        $parentCategoryId = $categories['newParent'];
        $index = $categories['newOrder'] + 1;

        $categoryIds = implode(';', $categories['categories']);

        try {
            $this->categoriesNewProvider->MoveCategories($language, $sessionId, $categoryIds, $parentCategoryId, $index);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }
}
