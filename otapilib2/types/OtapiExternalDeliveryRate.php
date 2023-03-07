<?php

class OtapiExternalDeliveryRate extends BaseOtapiType{
    /**
     * @return int
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? (string)$this->xmlData->Id : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetExternalDeliveryTypeId(){
        $value = isset($this->xmlData->ExternalDeliveryTypeId) ? (string)$this->xmlData->ExternalDeliveryTypeId : false;
        $propertyType = 'string';
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
     * @return double
     */
    public function GetStart(){
        $value = isset($this->xmlData->Start) ? (string)$this->xmlData->Start : false;
        $propertyType = 'double';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return double
     */
    public function GetStep(){
        $value = isset($this->xmlData->Step) ? (string)$this->xmlData->Step : false;
        $propertyType = 'double';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetIsEnabled(){
        $value = isset($this->xmlData->IsEnabled) ? (string)$this->xmlData->IsEnabled : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}