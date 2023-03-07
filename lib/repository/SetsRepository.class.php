<?php

class SetsRepository extends Repository
{
    private $setsUpdater;
    private $vendorRepository;
    private $fileMysqlMemoryCache;

    public function __construct($cms)
    {
        parent::__construct($cms);
        $this->setsUpdater = SetsUpdater::getInstance();
        $this->fileMysqlMemoryCache = new FileAndMysqlMemoryCache($cms);
        $this->cms->checkTable('site_sets_custom_images');
    }

    public function getRatingsList(array $setsParameters = array(), $lazyUpdate = false)
    {
        if ($lazyUpdate) {
            foreach ($setsParameters as $parameters) {
                $cacheKey = SetsUpdater::getRatingListCacheKey($parameters['contentType'], $parameters['type'], $parameters['catId'], Session::getActiveLang());
                if (!$this->fileMysqlMemoryCache->Exists($cacheKey)) {
                    // данные загрузятся через крон, а пока временно недоступно
                    throw new TemporarilyUnavailableException();
                }
            }
        }

        $language = Session::getActiveLang();
        $setsData = $this->setsUpdater->getData($language, $setsParameters);

        return $setsData;
    }

    public function ClearUnUsedSetsPictures($type, $items, $language)
    {
        $arrayOfIds = '';
        if (! empty($items)) {
            foreach ($items as $item) {
                $arrayOfIds .=  "'" . $item['Id'] . "',";
            }        
            $arrayOfIds = substr($arrayOfIds, 0, -1);        
            $this->cms->query("DELETE FROM `site_sets_custom_images` WHERE `typeId` = '" . $type . "' AND `language` = '" . $language . "' AND `item` NOT IN (" . $arrayOfIds . ")");
        }        
    }

    public function getSetsPictures($type, $items, $language)
    {
        foreach ($items as &$item) {
            $item['image_path'] = $this->cms->querySingleValue("SELECT `picture` FROM `site_sets_custom_images` WHERE `typeId` = '" . $type . "' AND `item` = '" . $item['Id'] . "' AND `language` = '" . $language . "'");
        }
        return $items;
    }

    public function addCutomImageForItems($items, $language)
    {
        $ids = array();
        foreach ($items as $key => $item) {
            $ids[] = "'" . $item['id'] . "'";
        }

        // получаем картинки для товаров, выбранные админом
        $result = array();
        $customImages = array();

        if (count($ids)) {
            $result = $this->cms->queryMakeArray("SELECT * FROM `site_sets_custom_images` WHERE `item` IN (" . implode(',', $ids ) . ") AND `language` = '" . $language . "' ORDER BY `id`");
        }
        foreach ($result as $image) {
            $customImages[$image['item']] =  $image['picture'];
        }

        // добавляем в товар картинку выбранную админом
        foreach ($items as $key => &$item) {
            if (isset($customImages[$item['id']])) {
                $item['image_path'] = $customImages[$item['id']];
            }
        }

        return $items;
    }

    public function addCutomImageForItem($item, $language) 
    {
        $items = $this->addCutomImageForItems(array($item), $language);
        return $items[0];
    }
    
    public function setPicture($type, $item, $picture, $language)
    {
        $this->deletePicture($type, $item, $language);
        $this->cms->query("INSERT INTO  `site_sets_custom_images` (`id` ,`typeId` ,`item` ,`picture` ,`language`) VALUES (NULL ,  '". $type . "',  '". $item . "',  '". $picture . "',  '". $language . "')");
    }
    
    public function deletePicture($type, $item, $language)
    {
        $this->cms->query("DELETE FROM `site_sets_custom_images` WHERE `typeId` = '" . $type . "' AND `language` = '" . $language . "' AND `item` = '" . $item . "'");
    }
    
    public function getSiteCategories() 
    {
        $categories = $this->cms->querySingleValue('SELECT properties FROM `site_blocks` WHERE type="sets_category"');
        $categories = json_decode($categories);
        if (! is_array($categories)) {
        	$categories = array();
        }
        return $categories; 
    }
}