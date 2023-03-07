<?php

class OtapiAdditionalVendorInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetName(){
        $value = isset($this->xmlData->Name) ? (string)$this->xmlData->Name : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiValueListOfFileInfo
     */
    public function GetImage(){
        $value = isset($this->xmlData->Image) ? $this->xmlData->Image : false;
        return new OtapiValueListOfFileInfo($value);
    }
    /**
     * @return OtapiMetaInfo
     */
    public function GetMetaInfo(){
        $value = isset($this->xmlData->MetaInfo) ? $this->xmlData->MetaInfo : false;
        return new OtapiMetaInfo($value);
    }
}