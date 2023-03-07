<?php

class OtapiBaseItemInfo extends OtapiAbstractCustomizablePrice{
    /**
     * @return string
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? (string)$this->xmlData->Id : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetErrorCode(){
        $value = isset($this->xmlData->ErrorCode) ? (string)$this->xmlData->ErrorCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function HasError(){
        $value = isset($this->xmlData->HasError) ? (string)$this->xmlData->HasError : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetProviderType(){
        $value = isset($this->xmlData->ProviderType) ? (string)$this->xmlData->ProviderType : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetTitle(){
        $value = isset($this->xmlData->Title) ? (string)$this->xmlData->Title : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetOriginalTitle(){
        $value = isset($this->xmlData->OriginalTitle) ? (string)$this->xmlData->OriginalTitle : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return long
     */
    public function GetMasterQuantity(){
        $value = isset($this->xmlData->MasterQuantity) ? (string)$this->xmlData->MasterQuantity : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCategoryId(){
        $value = isset($this->xmlData->CategoryId) ? (string)$this->xmlData->CategoryId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetExternalCategoryId(){
        $value = isset($this->xmlData->ExternalCategoryId) ? (string)$this->xmlData->ExternalCategoryId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetVendorId(){
        $value = isset($this->xmlData->VendorId) ? (string)$this->xmlData->VendorId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetVendorName(){
        $value = isset($this->xmlData->VendorName) ? (string)$this->xmlData->VendorName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return long
     */
    public function GetVendorScore(){
        $value = isset($this->xmlData->VendorScore) ? (string)$this->xmlData->VendorScore : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDescription(){
        $value = isset($this->xmlData->Description) ? (string)$this->xmlData->Description : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetBrandId(){
        $value = isset($this->xmlData->BrandId) ? (string)$this->xmlData->BrandId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetTaobaoItemUrl(){
        $value = isset($this->xmlData->TaobaoItemUrl) ? (string)$this->xmlData->TaobaoItemUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetExternalItemUrl(){
        $value = isset($this->xmlData->ExternalItemUrl) ? (string)$this->xmlData->ExternalItemUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetMainPictureUrl(){
        $value = isset($this->xmlData->MainPictureUrl) ? (string)$this->xmlData->MainPictureUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetStuffStatus(){
        $value = isset($this->xmlData->StuffStatus) ? (string)$this->xmlData->StuffStatus : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return long
     */
    public function GetVolume(){
        $value = isset($this->xmlData->Volume) ? (string)$this->xmlData->Volume : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiPrice
     */
    public function GetPrice(){
        $value = isset($this->xmlData->Price) ? $this->xmlData->Price : false;
        return new OtapiPrice($value);
    }
    /**
     * @return ArrayOfOtapiItemPicture
     */
    public function GetPictures(){
        $value = isset($this->xmlData->Pictures) ? $this->xmlData->Pictures : false;
        return new ArrayOfOtapiItemPicture($value);
    }
    /**
     * @return OtapiLocation
     */
    public function GetLocation(){
        $value = isset($this->xmlData->Location) ? $this->xmlData->Location : false;
        return new OtapiLocation($value);
    }
    /**
     * @return OtapiArrayOfString
     */
    public function GetFeatures(){
        $value = isset($this->xmlData->Features) ? $this->xmlData->Features : false;
        return new OtapiArrayOfString($value);
    }
    /**
     * @return OtapiArrayOfFeaturedValue
     */
    public function GetFeaturedValues(){
        $value = isset($this->xmlData->FeaturedValues) ? $this->xmlData->FeaturedValues : false;
        return new OtapiArrayOfFeaturedValue($value);
    }
    /**
     * @return boolean
     */
    public function IsSellAllowed(){
        $value = isset($this->xmlData->IsSellAllowed) ? (string)$this->xmlData->IsSellAllowed : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetSellDisallowReason(){
        $value = isset($this->xmlData->SellDisallowReason) ? (string)$this->xmlData->SellDisallowReason : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetApproxWeight(){
        $value = isset($this->xmlData->ApproxWeight) ? (string)$this->xmlData->ApproxWeight : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiPhysicalParameters
     */
    public function GetPhysicalParameters(){
        $value = isset($this->xmlData->PhysicalParameters) ? $this->xmlData->PhysicalParameters : false;
        return new OtapiPhysicalParameters($value);
    }
    /**
     * @return boolean
     */
    public function IsFiltered(){
        $value = isset($this->xmlData->IsFiltered) ? (string)$this->xmlData->IsFiltered : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfFilterReason
     */
    public function GetFilterReasons(){
        $value = isset($this->xmlData->FilterReasons) ? $this->xmlData->FilterReasons : false;
        return new OtapiArrayOfFilterReason($value);
    }
    /**
     * @return ArrayOfOtapiQuantityRange
     */
    public function GetQuantityRanges(){
        $value = isset($this->xmlData->QuantityRanges) ? $this->xmlData->QuantityRanges : false;
        return new ArrayOfOtapiQuantityRange($value);
    }
}