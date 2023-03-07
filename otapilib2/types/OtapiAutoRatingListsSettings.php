<?php

class OtapiAutoRatingListsSettings extends BaseOtapiType{
    /**
     * @return OtapiMetaInfo
     */
    public function GetMetaInfo(){
        $value = isset($this->xmlData->MetaInfo) ? $this->xmlData->MetaInfo : false;
        return new OtapiMetaInfo($value);
    }
    /**
     * @return boolean
     */
    public function AllowBackgroundUpdating(){
        $value = isset($this->xmlData->AllowBackgroundUpdating) ? (string)$this->xmlData->AllowBackgroundUpdating : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfString4
     */
    public function GetVendorids(){
        $value = isset($this->xmlData->Vendorids) ? $this->xmlData->Vendorids : false;
        return new OtapiArrayOfString4($value);
    }
    /**
     * @return int
     */
    public function GetItemsPerVendor(){
        $value = isset($this->xmlData->ItemsPerVendor) ? (string)$this->xmlData->ItemsPerVendor : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}