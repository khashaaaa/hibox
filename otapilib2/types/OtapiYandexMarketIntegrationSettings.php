<?php

class OtapiYandexMarketIntegrationSettings extends BaseOtapiType{
    /**
     * @return OtapiMetaInfo
     */
    public function GetMetaInfo(){
        $value = isset($this->xmlData->MetaInfo) ? $this->xmlData->MetaInfo : false;
        return new OtapiMetaInfo($value);
    }
    /**
     * @return string
     */
    public function GetItemRelativeUrlTemplate(){
        $value = isset($this->xmlData->ItemRelativeUrlTemplate) ? (string)$this->xmlData->ItemRelativeUrlTemplate : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetShopName(){
        $value = isset($this->xmlData->ShopName) ? (string)$this->xmlData->ShopName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetWebSiteUrl(){
        $value = isset($this->xmlData->WebSiteUrl) ? (string)$this->xmlData->WebSiteUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}