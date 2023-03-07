<?php

class OtapiCityInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetCountryId(){
        $value = isset($this->xmlData->CountryId) ? (string)$this->xmlData->CountryId : false;
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
    public function GetRegionName(){
        $value = isset($this->xmlData->RegionName) ? (string)$this->xmlData->RegionName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCityCode(){
        $value = isset($this->xmlData->CityCode) ? (string)$this->xmlData->CityCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCityName(){
        $value = isset($this->xmlData->CityName) ? (string)$this->xmlData->CityName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDisplayName(){
        $value = isset($this->xmlData->DisplayName) ? (string)$this->xmlData->DisplayName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}