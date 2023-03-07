<?php

class OtapiPickupPointInfo extends BaseOtapiType{
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
    public function GetCityCode(){
        $value = isset($this->xmlData->CityCode) ? (string)$this->xmlData->CityCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPickupPointCode(){
        $value = isset($this->xmlData->PickupPointCode) ? (string)$this->xmlData->PickupPointCode : false;
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
    public function GetGeoCoordinates(){
        $value = isset($this->xmlData->GeoCoordinates) ? (string)$this->xmlData->GeoCoordinates : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPostalCode(){
        $value = isset($this->xmlData->PostalCode) ? (string)$this->xmlData->PostalCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetAddress(){
        $value = isset($this->xmlData->Address) ? (string)$this->xmlData->Address : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}