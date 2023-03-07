<?php

class OtapiUserPreferences extends BaseOtapiType{
    /**
     * @return boolean
     */
    public function IsAnonymous(){
        $value = isset($this->xmlData->IsAnonymous) ? (string)$this->xmlData->IsAnonymous : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCountryCode(){
        $value = isset($this->xmlData->CountryCode) ? (string)$this->xmlData->CountryCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCountryName(){
        $value = isset($this->xmlData->CountryName) ? (string)$this->xmlData->CountryName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCountryFlagImageUrl(){
        $value = isset($this->xmlData->CountryFlagImageUrl) ? (string)$this->xmlData->CountryFlagImageUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCurrencyCode(){
        $value = isset($this->xmlData->CurrencyCode) ? (string)$this->xmlData->CurrencyCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCurrencySign(){
        $value = isset($this->xmlData->CurrencySign) ? (string)$this->xmlData->CurrencySign : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetExternalDeliveryId(){
        $value = isset($this->xmlData->ExternalDeliveryId) ? (string)$this->xmlData->ExternalDeliveryId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLanguage(){
        $value = isset($this->xmlData->Language) ? (string)$this->xmlData->Language : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}