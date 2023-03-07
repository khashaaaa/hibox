<?php

class OtapiBatchSimplifiedItemFullInfo extends OtapiBatchResultOfGeneralErrorCode{
    /**
     * @return OtapiSimplifiedId
     */
    public function GetCurrency(){
        $value = isset($this->xmlData->Currency) ? $this->xmlData->Currency : false;
        return new OtapiSimplifiedId($value);
    }
    /**
     * @return OtapiSimplifiedItemFullInfo
     */
    public function GetItem(){
        $value = isset($this->xmlData->Item) ? $this->xmlData->Item : false;
        return new OtapiSimplifiedItemFullInfo($value);
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
     * @return OtapiSimplifiedVendorInfo
     */
    public function GetVendor(){
        $value = isset($this->xmlData->Vendor) ? $this->xmlData->Vendor : false;
        return new OtapiSimplifiedVendorInfo($value);
    }
    /**
     * @return DataListOfOtapiCategory
     */
    public function GetRootPath(){
        $value = isset($this->xmlData->RootPath) ? $this->xmlData->RootPath : false;
        return new DataListOfOtapiCategory($value);
    }
    /**
     * @return OtapiDataSubListOfSimplifiedItemInfo
     */
    public function GetVendorItems(){
        $value = isset($this->xmlData->VendorItems) ? $this->xmlData->VendorItems : false;
        return new OtapiDataSubListOfSimplifiedItemInfo($value);
    }
    /**
     * @return OtapiDataSubListOfSimplifiedItemReview
     */
    public function GetProviderReviews(){
        $value = isset($this->xmlData->ProviderReviews) ? $this->xmlData->ProviderReviews : false;
        return new OtapiDataSubListOfSimplifiedItemReview($value);
    }
}