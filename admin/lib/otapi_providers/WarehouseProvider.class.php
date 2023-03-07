<?php

class WarehouseProvider
{
    /**
     * @var OTAPIlib
     */
    private $otapilib;

    /**
     * @var SetsProvider
     */
    private $setsProvider;

    /**
     * @var LanguageSettings
     */
    private $languagesProvider;

    public function __construct($otapilib)
    {
        $this->otapilib = $otapilib;
        $this->setsProvider = new SetsProvider(new CMS(), $otapilib);
        $this->languagesProvider = new LanguageSettings($otapilib);
    }

    public function GetEditableCategorySubcategories($sessionId, $parentCategoryId, $needHighlightParentsOfDeletedCategories, $predefinedData = "")
    {
        return $this->otapilib->GetEditableCategorySubcategories($sessionId, $parentCategoryId, $needHighlightParentsOfDeletedCategories, $predefinedData);
    }

    public function SearchDeletedCategoriesIds($sessionId, $predefinedData = "")
    {
        return $this->otapilib->SearchDeletedCategoriesIds($sessionId, $predefinedData);
    }

    public function SearchWarehouseCategories($sessionId, $xmlSearchParameters, $framePosition = 0, $frameSize = 18, $predefinedData = '')
    {
        return $this->otapilib->SearchWarehouseCategories($sessionId, $xmlSearchParameters, $framePosition,$frameSize, $predefinedData);
    }

    public function SearchWarehouseCategoriesXML($parent){
        $parentId = htmlentities($parent);
        $xml = new SimpleXMLElement('<SearchParameters></SearchParameters>');
        $xml->addChild('ParentId', $parentId);
        return $xml->asXML();
   }

    public function SearchWarehouseItems($sessionId, $xmlSearchParameters, $framePosition = 0, $frameSize = 18, $predefinedData = '')
    {
        return $this->otapilib->SearchWarehouseItems($sessionId, $xmlSearchParameters, $framePosition,$frameSize, $predefinedData);
    }

    public function SearchWarehouseItemsXML($category = false, $vendor = false, $name = false){
        $xml = new SimpleXMLElement('<SearchParameters></SearchParameters>');
        if($category) {
            $xml->addChild('CategoryId', $category);
        }
        if($vendor) {
            $xml->addChild('VendorId', $vendor);
        }
        if($name) {
            $xml->addChild('Name', $name);
        }
        return $xml->asXML();
    }

    public function UpdateWarehouseCategory($sessionId, $categoryId, $xmlUpdateData)
    {
        return $this->otapilib->UpdateWarehouseCategory($sessionId, $categoryId, $xmlUpdateData);
    }

    public function UpdateWarehouseCategoryInfo($sessionId, $categoryId, $name = null, $parentId = null)
    {
        $xml = new SimpleXMLElement('<CategoryData></CategoryData>');
        if (! is_null($name)) {
            $xml->addChild('Name', htmlspecialchars($name, ENT_QUOTES));
        }
        if (! is_null($parentId)) {
            $xml->addChild('ParentId', (int)$parentId);
        }
        if (count($xml->children())) {
            return $this->otapilib->UpdateWarehouseCategory($sessionId, $categoryId, $xml->asXML());
        }
    }

    public function UpdateWarehouseItem($sessionId, $itemId, $xmlUpdateData)
    {
        return $this->otapilib->UpdateWarehouseItem($sessionId, $itemId, $xmlUpdateData);
    }

    public function CreateWarehouseCategory($sessionId, $xmlCreateData)
    {
        return $this->otapilib->CreateWarehouseCategory($sessionId, $xmlCreateData);
    }

    public function CreateWarehouseCategoryData($sessionId, $name, $parentId, $position = null)
    {
        $xml = new SimpleXMLElement('<CategoryData></CategoryData>');
        $xml->addChild('Name', htmlspecialchars($name, ENT_QUOTES));
        $xml->addChild('ParentId', (int)$parentId);
        return $this->otapilib->CreateWarehouseCategory($sessionId, $xml->asXML());
    }

    public function CreateWarehouseItem($sessionId, $xmlCreateData)
    {
        return $this->otapilib->CreateWarehouseItem($sessionId, $xmlCreateData);
    }

    public function CreateWarehouseItemXML($name, $description, $images, $price, $quantity, $categoryId, $vendor, $weight, $resetWeight, $sellAllowed, $new){
        $xml = new DOMDocument('1.0', 'utf-8');
        $xml->formatOutput = true;
        $xml->preserveWhiteSpace = false;

        $itemData = $xml->createElement('ItemData');

        $el = $xml->createElement('Name', htmlspecialchars($name, ENT_QUOTES));
        $itemData->appendChild($el);
        $el = $xml->createElement('Description', htmlspecialchars($description, ENT_QUOTES));
        $itemData->appendChild($el);
        
        /*$el = $xml->createElement('MainImageUrl', htmlspecialchars($imageUrl, ENT_QUOTES));
        $itemData->appendChild($el);*/
        
        if (!is_array($images)) {
        	$images = array($images);
        }
        $imgs = $xml->createElement('ImageUrls');
        $main = true;
        foreach ($images as $i => $imgUrl) {
        	if(empty($imgUrl)) {
        		continue;
        	}
        	$img = $xml->createElement('ImageUrl', htmlspecialchars($imgUrl, ENT_QUOTES));
        	$img->setAttribute('IsMain', $main ? 'true' : 'false');
        	$imgs->appendChild($img);
        	$main = false;
        }
        $itemData->appendChild($imgs);
        
        
        $el = $xml->createElement('Price', htmlspecialchars($price, ENT_QUOTES));
        $itemData->appendChild($el);

        $el = $xml->createElement('IsSellAllowed', $sellAllowed ? 'true' : 'false');
        $itemData->appendChild($el);
        
        if ((! $sellAllowed && $quantity) || $new) {
	        $el = $xml->createElement('Quantity', htmlspecialchars($quantity, ENT_QUOTES));
	        $itemData->appendChild($el);
        }
        
        if (! $resetWeight) {
            $el = $xml->createElement('Weight', htmlspecialchars($weight, ENT_QUOTES));
            $itemData->appendChild($el);
        } else {
            $el = $xml->createElement('ResetWeight', true);
            $itemData->appendChild($el);
        }
        $el = $xml->createElement('CategoryId', htmlspecialchars($categoryId, ENT_QUOTES));
        $itemData->appendChild($el);

        $vendorData = $xml->createElement('Vendor');
        $vendorName = $xml->createElement('Name', htmlspecialchars($vendor, ENT_QUOTES));
        $vendorData->appendChild($vendorName);

        $itemData->appendChild($vendorData);

        $xml->appendChild($itemData);

        return $xml->saveXML();
    }

    public function IsWarehouseItem($id, $lang) {
        $itemInfo = $this->setsProvider->GetItemFullInfo($id, $lang);
        return $itemInfo['ProviderType'] === InstanceProvider::PROVIDER_TYPE_WAREHOUSE;
    }

    public function CreateWarehouseItemSimpleXML($data) {
        $xml = new DOMDocument('1.0', 'utf-8');
        $itemData = $xml->createElement('ItemData');

        if (isset($data['title'])) {
            $el = $xml->createElement('Name', htmlspecialchars($data['title'], ENT_QUOTES));
            $itemData->appendChild($el);
        }
        if (isset($data['description'])) {
            $el = $xml->createElement('Description', htmlspecialchars($data['description'], ENT_QUOTES));
            $itemData->appendChild($el);
        }
        if (isset($data['newImage'])) {
            $img = $xml->createElement('ImageUrl', htmlspecialchars($data['newImage'], ENT_QUOTES));
            $itemData->appendChild($img);
        }

        $xml->appendChild($itemData);

        return $xml->saveXML();

    }

    public function DeleteWarehouseCategory($sessionId, $categoryId)
    {
        return $this->otapilib->DeleteWarehouseCategory($sessionId, $categoryId);
    }

    public function DeleteWarehouseItem($sessionId, $itemId)
    {
        return $this->otapilib->DeleteWarehouseItem($sessionId, $itemId);
    }

    public function GetWarehouseCategoryInfo($sessionId, $categoryId)
    {
        return $this->otapilib->GetWarehouseCategoryInfo($sessionId, $categoryId);
    }

    public function GetWarehouseItemInfo($sessionId, $itemId)
    {
        return $this->otapilib->GetWarehouseItemInfo($sessionId, $itemId, "");
    }

    public function generateSearchParams($request = null)
    {
        $xmlParams = new SimpleXMLElement('<SearchParameters></SearchParameters>');
        if ($request && $request->getValue('Id')) {
            $xmlParams->addChild('Id', htmlspecialchars($request->getValue('Id'), ENT_QUOTES));
        }
        if ($request && $request->getValue('Name')) {
            $xmlParams->addChild('Name', htmlspecialchars($request->getValue('Name'), ENT_QUOTES));
        }
        if ($request && $request->getValue('ParentId')) {
            $xmlParams->addChild('ParentId', htmlspecialchars($request->getValue('ParentId'), ENT_QUOTES));
        } else {
            $xmlParams->addChild('ParentId', 0);
        }
        if ($request && $request->getValue('IsParent')) {
            $xmlParams->addChild('IsParent', htmlspecialchars($request->getValue('IsParent'), ENT_QUOTES));
        }

        return str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    }

    public function GetProviderInfo($providerType, $predefinedData = "")
    {
        return $this->otapilib->GetProviderInfo($providerType, $predefinedData);
    }

    public function RunWarehouseImportItems($lang, $sessionId, $sourceFileId)
    {
        $xml = new SimpleXMLElement('<ItemImportData></ItemImportData>');
        if (! is_null($sourceFileId)) {
            $xml->addChild('SourceFileId', htmlspecialchars($sourceFileId, ENT_QUOTES));
        }

        $answer = null;
        OTAPILib2::RunWarehouseImportItems($lang, $sessionId, $xml->asXML(), $answer);
        OTAPILib2::makeRequests();

        return $answer;
    }
}
