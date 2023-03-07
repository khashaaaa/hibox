<?php

class OtapiSearchBrandInfo extends BaseOtapiType{
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
    public function GetBrandName(){
        $value = isset($this->xmlData->BrandName) ? (string)$this->xmlData->BrandName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}