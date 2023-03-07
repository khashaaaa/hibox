<?php
OTBase::import('SeoCategoryRepository');

class CategoriesProvider extends Repository
{
    private $currentActiveLang;

    /**
     * @var OTAPIlib
     */
    private $otapilib;
    private $seoRepository;

    public function __construct($cms, $otapilib)
    {
        parent::__construct($cms);
        $this->otapilib = $otapilib;
        $this->seoRepository = new SeoCategoryRepository($this->cms);
    }

    public function DeleteCatalogSeo()
    {
        // удалить алиасы site_categories
        $this->deleteCategoryAlias();
        // удалить сео текст site_pages_langs_data
        $this->deleteCategorySEO();
        // удалить сео данные site_categories_seo_texts
        $this->deleteSeoText();
    }

    public function DeleteCatalogSeoFrames(array $categories)
    {
        // удалить алиасы site_categories
        $this->seoRepository->deleteCategoryAliasFrames($categories);
        // удалить сео текст site_pages_langs_data
        $this->seoRepository->deleteCategorySEOFrames($categories);
        // удалить сео данные site_categories_seo_texts
        $this->deleteSeoTextFrames($categories);
    }

    public function RemoveCategory($sessionId, $categoryId) 
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->RemoveCategory($sessionId, $categoryId);
        $this->seoRepository->removeCategoryByCategoryId($categoryId);

        $this->afterCategoriesOtapiRequest();
        return $response;
    }

    public function EditCategoryExternalId($categoryId, $externalCategoryId, $sessionId, $predefinedData = "")
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->EditCategoryExternalId($categoryId, $externalCategoryId, $sessionId);

        $this->afterCategoriesOtapiRequest();
        return $response;
    }
    
    public function EditOrderOfCategory($index, $categoryId, $sessionId, $predefinedData = "")
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->EditOrderOfCategory($index, $categoryId, $sessionId, $predefinedData);

        $this->afterCategoriesOtapiRequest();
        return $response;
    }
    
    public function EditCategoriesVisible($categoriesVisibleSettings, $sessionId, $predefinedData = "")
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->EditCategoriesVisible($categoriesVisibleSettings, $sessionId, $predefinedData);

        $this->afterCategoriesOtapiRequest();
        return $response;
    }
    
    public function EditCategoryParent($sessionId, $categoryId, $parentCategoryId, $predefinedData = "")
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->EditCategoryParent($sessionId, $categoryId, $parentCategoryId);

        $this->afterCategoriesOtapiRequest();
        return $response;
    }
    
    public function EditCategoryNameByLanguage($sessionId, $categoryId, $categoryName)
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->EditCategoryNameByLanguage($sessionId, $categoryId, $categoryName);

        $this->afterCategoriesOtapiRequest();
        return $response;
    }
    
    public function GetEditableCategorySubcategories($sessionId, $parentCategoryId, $needHighlightParentsOfDeletedCategories, $predefinedData = "")
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->GetEditableCategorySubcategories($sessionId, $parentCategoryId, $needHighlightParentsOfDeletedCategories, $predefinedData);

        $this->afterCategoriesOtapiRequest();
        return $response;
    }
    
    public function GetCategorySubcategoryInfoList($parentCategoryId, $predefinedData = "")
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->GetCategorySubcategoryInfoList($parentCategoryId, $predefinedData);

        $this->afterCategoriesOtapiRequest();
        return $response;
    }
    
    public function GetProviderCategorySubcategories($parentCategoryId, $predefinedData = "")
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->GetProviderCategorySubcategories($parentCategoryId, $predefinedData);

        $this->afterCategoriesOtapiRequest();
        return $response;
    }
    
    public function GetProviderCategory($externalId, $predefinedData = "")
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->GetProviderCategory($externalId, $predefinedData = "");

        $this->afterCategoriesOtapiRequest();
        return $response;
    }
    
    public function GetCategoryInfo($externalId, $predefinedData = "")
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->GetCategoryInfo($externalId, $predefinedData = "");

        $this->afterCategoriesOtapiRequest();
        return $response;
    }
    
    public function GetRegionName($regionId)
    {
        $regionName = LangAdmin::get('Undefined');
        $regions = $this->otapilib->GetAllAreaList(Session::getActiveLang());
        foreach ($regions as $region) {
            if ($region['Id'] == $regionId) {
                $regionName = $region['Name'];
                break;
            }
        }
        return $regionName;
    }
     
    
    public function AddCategoryByLanguage($sessionId, $categoryName, $parentCategoryId, $categoryId, $predefinedData = "")
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->AddCategoryByLanguage($sessionId, $categoryName, $parentCategoryId, $categoryId, $predefinedData);

        $this->afterCategoriesOtapiRequest();
        return $response;
    }

    public function SearchDeletedCategoriesIds($sessionId, $predefinedData = "")
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->SearchDeletedCategoriesIds($sessionId, $predefinedData);

        $this->afterCategoriesOtapiRequest();
        return $response;
    }
    
    public function ExportStructureByLanguage($sessionId, $predefinedData = "")
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->ExportStructureByLanguage($sessionId, $predefinedData);

        $this->afterCategoriesOtapiRequest();
        return $response;
    }    

    public function ImportStructureByLanguage($sessionId, $source, $predefinedData = "")
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->ImportStructureByLanguage($sessionId, $source, $predefinedData);

        $this->afterCategoriesOtapiRequest();
        return $response;
    }
    
    public function ImportCatalog($sessionId, $source, $predefinedData = "")
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->ImportCatalog($sessionId, $source, $predefinedData);
        
        $this->afterCategoriesOtapiRequest();
        return $response;
    }
    
    public function ImportSubCatalogTree($sessionId, $parentCategoryId, $xmlCatalog, $predefinedData = "") {
    	$this->beforeCategoriesOtapiRequest();
        $this->otapilib->setCurlTimeout(5*60);

    	$response = $this->otapilib->ImportSubCatalogTree($sessionId, $parentCategoryId, $xmlCatalog, $predefinedData);

        $this->otapilib->setCurlTimeout(60);
    	$this->afterCategoriesOtapiRequest();
    	return $response;
    }

    public function ExportCatalog($sessionId, $predefinedData = "")
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->ExportCatalog($sessionId, $predefinedData);

        $this->afterCategoriesOtapiRequest();
        return $response;
    }
    
    public function ExportSubCatalogTree($sessionId, $parentCategoryId, $predefinedData = ""){
    	$this->beforeCategoriesOtapiRequest();
    	
    	$response = $this->otapilib->ExportSubCatalogTree($sessionId, $parentCategoryId, $predefinedData);
    	
    	$this->afterCategoriesOtapiRequest();
    	return $response;
    }
    
    public function EditCategoryInfo($sessionId, $categoryId, $xmlCategoryInfo, $predefinedData = "")
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->EditCategoryInfo($sessionId, $categoryId, $xmlCategoryInfo, $predefinedData);

        $this->afterCategoriesOtapiRequest();
        return $response;
    }
    
    public function getCategoryAlias($cid, $cname = '')
    {
        return $this->seoRepository->getCategoryAlias($cid, $cname);
    }
    
    public function getCategoriesAliases()
    {
        return $this->seoRepository->getCategoriesAliases();
    }
    
    public function getCategoryAliases($cid) 
    {
        $q = 'SELECT `id`, `alias`, `category_id` FROM `site_categories` WHERE `category_id`="' . General::getCms()->escape($cid) . '"';
        $aliases = $this->cms->queryMakeArray($q, array('site_categories')); 
        return $aliases ? $aliases : array();
    }
    
    public function getCategorySEO($cid, $language = 'ru')
    {
        return $this->seoRepository->getCategorySEO($cid, $language);
    }

    public function getBrandSEO($id, $language = 'ru')
    {
        return $this->seoRepository->getBrandSEO($id, $language);
    }


    public function getVendorSEO($sellerId, $language = 'ru')
    {
        return $this->seoRepository->getVendorSEO($sellerId, $language);
    }

    public function getLangsCategorySEO()
    {
        return $this->seoRepository->getLangsCategorySEO();
    }
    
    
    public function setCategoryAliasEx($cid, $alias, $aliasId = 'new') {
        $this->cms->checkTable('site_categories');
        if (is_numeric($aliasId)) {
            $this->cms->query('UPDATE `site_categories` SET `alias`="' . General::getCms()->escape($alias) . '" WHERE `category_id`="' . General::getCms()->escape($cid) . '" and id="' . General::getCms()->escape($aliasId) . '"');
            return $aliasId;
        } else {
            $this->cms->query('INSERT INTO `site_categories` SET `category_id`="' . General::getCms()->escape($cid) . '", `alias`="' . General::getCms()->escape($alias) . '"');
            return General::getCms()->insertedId();
        }
    }
    
    public function setCategoryAlias($cid, $alias, $showError = false, $defineIsset = null)
    {
        return $this->seoRepository->setCategoryAlias($cid, $alias, $showError, $defineIsset);
    }
    
    public function checkCategoryAlias($alias)
    {
        return $this->seoRepository->checkCategoryAlias($alias);
    }

    public function checkVendorName($url)
    {
        return $this->seoRepository->checkVendorName($url);
    }

    public function checkVendorAlias($alias, $sellerId)
    {
        return $this->seoRepository->checkVendorAlias($alias, $sellerId);
    }

    public function setCategorySEO($data, $defineIsset = null)
    {
        return $this->seoRepository->setCategorySEO($data, $defineIsset);
    }

    public function setBrandSEO($data, $defineIsset = null)
    {
        return $this->seoRepository->setBrandSEO($data, $defineIsset);
    }

    public function setVendorSEO($data, $defineIsset = null)
    {
        return $this->seoRepository->setVendorSEO($data, $defineIsset);
    }

    public function deleteCategoryAlias($categoryId = '', $language = '')
    {
        return $this->seoRepository->deleteCategoryAlias($categoryId, $language);
    }

    public function deleteCategorySEO($categoryId = '', $language = '')
    {
        return $this->seoRepository->deleteCategorySEO($categoryId, $language);
    }

    public function deleteSeoText($categoryId = '', $language = '')
    {
        $this->cms->Check();
        $categoryId = General::getCms()->escape($categoryId);
        $language = General::getCms()->escape($language);

        if (empty($categoryId)) {
            // очистить всю таблицу
            $sql = 'TRUNCATE site_categories_seo_texts';
        } else {
            if (empty($lang)) {
                // очистить СЕО текст категирии для всех языковых версий
                $sql = 'DELETE FROM `site_categories_seo_texts` WHERE category_id = "' . $categoryId . '"';
            } else {
                // очистить СЕО текст категории для выбранного языка
                $sql = 'DELETE FROM `site_categories_seo_texts` WHERE category_id = "' . $categoryId . '" AND lang_code = "' . $language . '"';
            }
        }

        return General::getCms()->query($sql, array('site_categories_seo_texts'));
    }

    public function deleteSeoTextFrames(array $categoriesFrames, $language = '')
    {
        $this->cms->Check();
        $language = $this->cms->escape($language);

        foreach ($categoriesFrames as $categoriesFrame) {
            if (is_array($categoriesFrame)) {
                $categoriesFrame = '"' . implode('","', $categoriesFrame) . '"';
                if (!empty($categoriesFrame)) {
                    if (empty($lang)) {
                        // очистить СЕО категирий для всех языковых версий
                        $sql = 'DELETE FROM `site_categories_seo_texts` WHERE category_id IN (' . $categoriesFrame . ')';
                    } else {
                        // очистить СЕО категорий для выбранного языка
                        $sql = 'DELETE FROM `site_categories_seo_texts` WHERE category_id IN (' . $categoriesFrame . ') AND lang_code = "' . $language . '"';
                    }
                    $this->cms->query($sql, array('site_categories_seo_texts'));
                }
            }
        }
    }

    public function getSeoText($id, $language)
    {
        $this->cms->Check();
        $this->cms->checkTable('site_categories_seo_texts');
        $id = General::getCms()->escape($id);
        return General::getCms()->querySingleValue('SELECT text FROM `site_categories_seo_texts` WHERE `category_id`="'.$id.'" AND `lang_code`="'.$language.'"');
    }
    
    public function getLangsSeoText()
    {
        $this->cms->Check();
        $this->cms->checkTable('site_categories_seo_texts');
        $data = General::getCms()->queryMakeArray('SELECT * FROM  `site_categories_seo_texts`');
        $result = array();
        foreach ($data as $d) {
            if (empty($result[$d['category_id']])) $result[$d['category_id']] = array();
            $result[$d['category_id']][$d['lang_code']] = $d;
        }
        return $result;
    }
    
    public function setSeoText($id, $text, $language, $defineIsset = null)
    {
        $cms = General::getCms();

        if (is_null($defineIsset)) {
            $isset = $cms->querySingleValue('SELECT COUNT(*) FROM `site_categories_seo_texts` WHERE `category_id`="' . $cms->escape($id) . '" AND `lang_code`="' . $cms->escape($language) . '"'); //
        } else {
            $isset = $defineIsset;
        }

        if ($isset) {
            $result = $cms->query('UPDATE `site_categories_seo_texts` SET 
                                      `text`="' . $cms->escape(stripslashes($text)) . '" 
                                    WHERE 
                                        `category_id`="' . $cms->escape($id) . '" AND 
                                        `lang_code`="' . $cms->escape($language) . '"');
        } else {
            $result = $cms->query('INSERT INTO `site_categories_seo_texts` SET 
                                      `category_id`="' . $id . '", 
                                      `text`="' . $cms->escape(stripslashes($text)) . '", 
                                      `lang_code`="' . $language . '"');
        }
        return $result;
    }
    
    public function BatchSearchItemsFrame($categoryId, $providerType)
    {
        $this->beforeCategoriesOtapiRequest();

        $categoryItemFilter = '<SearchItemsParameters>'
            . '<Provider>' . $providerType . '</Provider>'
            . '<CategoryId>' . $categoryId . '</CategoryId>'
            . '<OutputMode>TotalCount</OutputMode></SearchItemsParameters>';
                
        $response = $this->otapilib->BatchSearchItemsFrame(session_id(), $categoryItemFilter, 0, 1, 'SearchProperties');
        $this->afterCategoriesOtapiRequest();
        return $response;
    }
    
    public function GetCategorySearchProperties($categoryId)
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->GetCategorySearchProperties($categoryId);

        $this->afterCategoriesOtapiRequest();
        return $response;
    }
    
    public function updateSearchFilter($categoryId, $type, $text, $sessionId, $langId)
    {
        $this->beforeCategoriesOtapiRequest();
            	
        switch ($type) {
            case 'ItemPropertyName': {
                $key = (string)(CFG_SERVICE_INSTANCEKEY . ':taobao:ItemProperty:Name');
                $this->otapilib->EditTranslateByKey($sessionId, $langId, $text, $key, $categoryId);
            } break;
            case 'ItemPropertyValueName': {
                $key = (string)(CFG_SERVICE_INSTANCEKEY . ':taobao:ItemPropertyValue:Name');
                $this->otapilib->EditTranslateByKey($sessionId, $langId, $text, $key, $categoryId);
            } break;
            default:
                break;
        }

        $this->afterCategoriesOtapiRequest();
    }
    
    public function FindHintCategoryInfoList($hintTitle, $predefinedData = "")
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->FindHintCategoryInfoList($hintTitle, $predefinedData);

        $this->afterCategoriesOtapiRequest();
        return $response;
    }
    
    public function GetRootCategoryInfoList()
    {
        $this->beforeCategoriesOtapiRequest();

        $response = $this->otapilib->GetRootCategoryInfoList();

        $this->afterCategoriesOtapiRequest();
        return $response;
    }
    
    public function GetProviderInfoList()
    {
        $this->beforeCategoriesOtapiRequest();
        $this->otapilib->setUseAdminLangOn();
        $providerInfoList = InstanceProvider::getObject()->GetProviderInfoList(Session::getActiveAdminLang());
        $response = $this->otapilib->GetProviderInfoList($providerInfoList->asXML());

        $this->afterCategoriesOtapiRequest();
        return $response;
    }

    /*
     * запросы делаются согластно активному языку страницы "Категории"
     */
    private function beforeCategoriesOtapiRequest()
    {
        $this->currentActiveLang = Session::getActiveLang();
        $this->otapilib->setUseAdminLangOff();
        $language = Session::get('active_lang_categories') ? Session::get('active_lang_categories') : Session::getActiveAdminLang();
        Session::setActiveLang($language);
    }

    /*
     * возвращаем язык витрины и включаем запросы отапи по языку админки
     */
    private function afterCategoriesOtapiRequest()
    {
        Session::setActiveLang($this->currentActiveLang);
        $this->otapilib->setUseAdminLangOn();
    }
}

