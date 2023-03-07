<?php

class SetsProvider extends Repository
{
    /**
     * @var OTAPIlib
     */
    private $otapilib;
    private $setsRepository;
    
    public function __construct($cms, $otapilib)
    {
        parent::__construct($cms);
        $this->otapilib = $otapilib;
        $this->setsRepository = new SetsRepository($this->cms);
    }

    public function GetBrandRatingList($itemRatingTypeId, $numberItem, $categoryId, $language = 'ru', $predefinedData = false)
    {
        return $this->otapilib->GetBrandRatingList($itemRatingTypeId, $numberItem, $categoryId, $language, $predefinedData = false);
    }

    public function RemoveElementsSetRatingList($sessionId, $itemRatingTypeId, $contentType, $categoryId, $itemList, $predefinedData = "")
    {
        return $this->otapilib->RemoveElementsSetRatingList($sessionId, $itemRatingTypeId, $contentType, $categoryId, $itemList, $predefinedData);
    }

    public function RemoveAllElementsRatingList($sessionId, $itemRatingTypeId, $contentType, $categoryId, $predefinedData = "")
    {
        return $this->otapilib->RemoveAllElementsRatingList($sessionId, $itemRatingTypeId, $contentType, $categoryId, $predefinedData);
    }

    public function AddElementsSetToRatingList($sessionId, $itemRatingTypeId, $contentType, $categoryId, $itemList, $predefinedData = "")
    {
        return $this->otapilib->AddElementsSetToRatingList($sessionId, $itemRatingTypeId, $contentType, $categoryId, $itemList, $predefinedData);
    }

    public function GetBrandInfoList($predefinedData = "")
    {
        return $this->otapilib->GetBrandInfoList($predefinedData);
    }

    public function GetVendorRatingList($itemRatingTypeId, $numberItem, $categoryId, $language = 'ru', $predefinedData = false)
    {
        return $this->otapilib->GetVendorRatingList($itemRatingTypeId, $numberItem, $categoryId, $language, $predefinedData);
    }

    public function SearchRatingListVendors($xmlSearchParameters, $framePosition = 0, $frameSize = 18, $predefinedData = "")
    {
        return $this->otapilib->SearchRatingListVendors($xmlSearchParameters, $framePosition, $frameSize, $predefinedData);
    }

    public function GetVendorInfo($vendorId, $predefinedData = "")
    {
        return $this->otapilib->GetVendorInfo($vendorId, $predefinedData);
    }

    public function saveSetSellerInfo($vendorId, $alias, $language = 'ru')
    {
        $vendorId = $this->cms->escape($vendorId);

        $sql = 'INSERT INTO `site_vendors_images`
                SET `vendor_id`="' . $vendorId . '"
                , `lang`="' . $language . '"
                , `alias`="' . $alias . '"
                ';

        return $this->cms->query($sql, array('site_vendors_images'));
    }
    
    public function updateSetSellerInfo($vendorId, $alias, $language = 'ru')
    {
        $vendorId = $this->cms->escape($vendorId);
        if (!empty($alias)) {
            $sql = 'UPDATE `site_vendors_images` SET `alias`="' . $alias .  '" WHERE `vendor_id`="' . $vendorId .'"';
            $this->cms->query($sql, array('site_vendors_images'));
        }
        $sql = 'UPDATE `site_vendors_images` SET `alias`="' . $alias . '"  WHERE `vendor_id`="' . $vendorId .'" and `lang`="' . $language . '" ';
        return $this->cms->query($sql, array('site_vendors_images'));
    }
    

    public function getSetSellerInfo($id, $language = 'ru')
    {
        $id = $this->cms->escape($id);
        $language = $this->cms->escape($language);

        $sql = 'SELECT * FROM `site_vendors_images` WHERE `vendor_id`="' . $id . '" AND `lang`="' . $language . '"';
        $q = $this->cms->query($sql, array('site_vendors_images'));

        return @mysqli_fetch_assoc($q);
    }

    public function deleteSetSellerInfo($id, $language = 'ru')
    {
        $vendorId = $this->cms->escape($id);
        $language = $this->cms->escape($language);

        $sql = 'DELETE FROM `site_vendors_images` WHERE `vendor_id`="' . $vendorId . '" AND `lang`="' . $language . '"';

        return $this->cms->query($sql, array('site_vendors_images'));
    }
    
    public function GetItemRatingList($ItemRatingType, $frameSize, $categoryId, $language, $framePosition = 0)
    {
        $xmlParams = new SimpleXMLElement('<RatingListItemSearchParameters></RatingListItemSearchParameters>');
        $xmlParams->addChild('CategoryId', htmlspecialchars($categoryId));
        $xmlParams->addChild('ItemRatingType', htmlspecialchars($ItemRatingType));
        $xml = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
        
        $answer = array();
        OTAPILib2::SearchRatingListItems($language, $xml, $framePosition, $frameSize, $answer);
        OTAPILib2::makeRequests();
        
        $data = array();
        $itemsList = array();
        $data['totalCount'] = $answer->GetOtapiItemInfoSubList()->GetTotalCount();
        $list = $answer->GetOtapiItemInfoSubList()->GetContent();
        foreach ($list->getItem() as $key => $item) {
            $listItem = array();
            $listItem['Id'] = $item->GetId();
            $listItem['Title'] = $item->GetTitle();
            $listItem['OriginalTitle'] = $item->GetOriginalTitle();
            $listItem['CategoryId'] = $item->GetCategoryId();
            $listItem['MainPictureUrl'] = $item->GetMainPictureUrl();
            $listItem['id'] = $item->GetId();
            $listItem['title'] = $item->GetTitle();
            $listItem['originaltitle'] = $item->GetOriginalTitle();
            $listItem['categoryid'] = $item->GetCategoryId();
            $listItem['mainpictureurl'] = $item->GetMainPictureUrl();
            
/*            $listItem['ProviderType'] = $item->GetProviderType();
            $listItem['ExternalCategoryId'] = $item->GetExternalCategoryId();
            $listItem['VendorId'] = $item->GetVendorId();
            $listItem['VendorName'] = $item->GetVendorName();
            $listItem['VendorScore'] = $item->GetVendorScore();
            $listItem['ExternalItemUrl'] = $item->GetExternalItemUrl();
            $listItem['StuffStatus'] = $item->GetStuffStatus();
            $listItem['Volume'] = $item->GetVolume();
            $listItem['IsSetAllowed'] = $item->IsSellAllowed();
            $listItem['SellDisallowReason'] = $item->GetSellDisallowReason();
            $listItem['IsFiltered'] = $item->IsFiltered();
            
            $listItem['Price'] = array();
            $listItem['Price']['ConvertedPrice'] = $item->GetPrice()->GetConvertedPrice();
            $listItem['Price']['ConvertedPriceWithoutSign'] = $item->GetPrice()->GetConvertedPriceWithoutSign();
            $listItem['Price']['CurrencySign'] = $item->GetPrice()->GetCurrencySign();
            $listItem['Price']['CurrencyName'] = $item->GetPrice()->GetCurrencyName();
            $listItem['Price']['IsDeliverable'] = $item->GetPrice()->IsDeliverable();
            
            $listItem['Price']['DeliveryPrice'] = array(
                'OriginalPrice' => $item->GetPrice()->GetDeliveryPrice()->GetOriginalPrice(),
                'MarginPrice' => $item->GetPrice()->GetDeliveryPrice()->GetMarginPrice(),
                'OriginalCurrencyCode' => $item->GetPrice()->GetDeliveryPrice()->GetOriginalCurrencyCode(),
/*                GetConvertedPriceList
                    GetInternal
                        GetSignAttribute
                        GetCodeAttribute
                    GetDisplayedMoneys
                        GetMoney
                            GetSignAttribute
                            GetCodeAttribute*/
/*                ),
            $listItem['Price']['OneItemDeliveryPrice'] = $item->GetPrice()->GetOneItemDeliveryPrice();
            $listItem['Price']['PriceWithoutDelivery'] = $item->GetPrice()->GetPriceWithoutDelivery();

            $listItem['Pictures'] = array();
            foreach ($item->GetPictures()->GetItemPicture() as $ikey => $picture) {
                $listItem['Pictures'][] = array(
                    'Url' => $picture->GetUrl(),
                    'SmallUrl' => array('Width' => $picture->GetSmall()->GetWidthAttribute(), 'Height' => $picture->GetSmall()->GetHeightAttribute()),
                    'MediumUrl' => array('Width' => $picture->GetMedium()->GetWidthAttribute(), 'Height' => $picture->GetMedium()->GetHeightAttribute()),
                    'LargeUrl' => array('Width' => $picture->GetLarge()->GetWidthAttribute(), 'Height' => $picture->GetLarge()->GetHeightAttribute()),
                    'IsMain' => $picture->IsMain()
                    );
            }*/
            
            $itemsList[] = $listItem;
        }

        $data['items'] = $itemsList; 
        return $data;
    }
    
    public function EditTranslateByKey($sessionId, $lang, $text, $key, $idInContext, $predefinedData = "")
    {
        return $this->otapilib->EditTranslateByKey($sessionId, $lang, $text, $key, $idInContext, $predefinedData);
    }
    
    public function GetItemFullInfo($itemId, $language, $predefinedData = "")
    {
        return $this->otapilib->GetItemFullInfo($itemId, $language, $predefinedData);
    }
    
    public function GetItemDescription($itemId, $language, $predefinedData = "")
    {
        return $this->otapilib->GetItemDescription($itemId, $language, $predefinedData);
    }
    
    public function ResetInstanceCaches($predefinedData = "")
    {
        return $this->otapilib->ResetInstanceCaches($predefinedData);
    }
    
    public function GetItemCustomPictures($type, $items, $language)
    {
        return $this->setsRepository->getSetsPictures($type, $items, $language);
    }
    
    public function ClearUnUsedItemCustomPictures($type, $items, $language)
    {
        return $this->setsRepository->ClearUnUsedSetsPictures($type, $items, $language);
    }    
    
    public function SetItemCustomPictures($type, $item, $picture, $language)
    {
        return $this->setsRepository->setPicture($type, $item, $picture, $language);
    }
    
    public function DelItemCustomPictures($type, $item, $language)
    {
        return $this->setsRepository->deletePicture($type, $item, $language);
    }
    
    public function GetAllSetCategories($sessionId, $offset, $count){
        $categories = array();
        $xmlParams = '';
        
        $siteCategories = $this->cms->querySingleValue('SELECT properties FROM `site_blocks` WHERE type="sets_category"');
        $siteCategories = json_decode($siteCategories);
        if (! is_array($siteCategories)) {
            $siteCategories = array();
        }
        $xmlParams = new SimpleXMLElement('<RatingListSearchParameters></RatingListSearchParameters>');
        $xmlParams->addChild('ItemRatingType', 'Category');
        $xmlParams->addChild('ContentType', 'Item');
        $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>','',$xmlParams->asXML());
        
        $totalCount = 0;
        
        $list = $this->otapilib->SearchRatingLists($sessionId, $xml, $offset, $count);
        $ids = array();
        $totalCount = $list['totalcount'];
        
        if (isset($list['content'])) {
            foreach ($list['content'] as $key => $item) {
                $categories[$item['CategoryId']] = $item;
                $categories[$item['CategoryId']]['name'] = $item['CategoryId']; 
                $categories[$item['CategoryId']]['onSite'] = in_array($item['CategoryId'], $siteCategories); 
                $ids[] = $item['CategoryId'];
            }
        }
        
        if (count($ids) > 0 ){
            $data = $this->otapilib->GetCategoryInfoList(implode(',', $ids));
            foreach ($data as $key => $cat) {
                $categories[$cat['id']]['name'] = $cat['name'];   
            }
        }
        
        return array('list' => $categories, 'totalCount' => $totalCount);
    }
    
    public function GetSiteSetCategories($sessionId) 
    {
        $categories = $this->cms->querySingleValue('SELECT properties FROM `site_blocks` WHERE type="sets_category"');
        $categories = json_decode($categories);   
        if (! is_array($categories)) {
        	$categories = array();
        }

        if (empty($categories)) {
            return array();
        }

        $ratingCategories = array();
        //get site sets categories data
        $xmlParams = new SimpleXMLElement('<RatingListSearchParameters></RatingListSearchParameters>');
        $xmlParams->addChild('ItemRatingType', 'Category');
        $xmlParams->addChild('ContentType', 'Item');
        $idList = $xmlParams->addChild('CategoryIdList');
        foreach ($categories as $key => $id) {
            $idList->addChild('string', $id); 
        }
        $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>','',$xmlParams->asXML());
        $list = $this->otapilib->SearchRatingLists($sessionId, $xml, 0, count($categories));

        $ids = array();        
        if (isset($list['content'])) {
            foreach ($list['content'] as $key => $item) {
                $ratingCategories[$item['CategoryId']] = $item;
                $ratingCategories[$item['CategoryId']]['name'] = isset($item['name']) ? $item['name'] : $item['CategoryId'];
                $ids[] = $item['CategoryId'];
            }
        }
        
        if (count($ids) > 0 ){
            $data = $this->otapilib->GetCategoryInfoList(implode(',', $ids));
            foreach ($data as $key => $cat) {
                $ratingCategories[$cat['id']]['name'] = $cat['name'];
            }
        }

        // выводим в выбранном порядке
        $siteSetCategories = array();
        foreach ($categories as $id) {
            if (isset($ratingCategories[$id])) {
                $siteSetCategories[] = $ratingCategories[$id];
            }
        }

        return $siteSetCategories;
    }
    
    public function SetSiteCategoriesSet($categories = array()) 
    {
        $found = $this->cms->querySingleValue('SELECT COUNT(*) FROM `site_blocks` WHERE type="sets_category"');
        $props = json_encode($categories);
        if ($found) {
            $this->cms->query('UPDATE `site_blocks` SET `properties`="' . $this->cms->escape($props) . '" WHERE type="sets_category"');
        } else {
            $this->cms->query('INSERT INTO `site_blocks` SET `properties`="' . $this->cms->escape($props) . '", `type`="sets_category"');
        }
    }
    
    public function setItemPosition($language, $sessionId, $type, $contentType, $cid, $id, $position)
    {
        $answer = array();
    
        $xmlParams = new SimpleXMLElement('<RatingListElementsInsertData></RatingListElementsInsertData>');
        $xmlParams->addChild('ItemRatingType', htmlspecialchars($type));
        $xmlParams->addChild('CategoryId', htmlspecialchars($cid));
        $idList = $xmlParams->addChild('ElementIdList');
        $idList->AddChild('Id', htmlspecialchars($id));
        $xmlParams->addChild('IndexNumber', htmlspecialchars($position));
        $xmlParams->addChild('ContentType', $contentType);
    
        $xml = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    
        $answer = array();
        OTAPILib2::InsertElementsSetToRatingList($language, $sessionId, $xml, $answer);
        OTAPILib2::makeRequests();
    }    
}

