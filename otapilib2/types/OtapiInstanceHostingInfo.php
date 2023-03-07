<?php

class OtapiInstanceHostingInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetName(){
        $value = isset($this->xmlData->Name) ? (string)$this->xmlData->Name : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return dateTime
     */
    public function GetActivationDate(){
        $value = isset($this->xmlData->ActivationDate) ? (string)$this->xmlData->ActivationDate : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return dateTime
     */
    public function GetExpirationDate(){
        $value = isset($this->xmlData->ExpirationDate) ? (string)$this->xmlData->ExpirationDate : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiMoney
     */
    public function GetCost(){
        $value = isset($this->xmlData->Cost) ? $this->xmlData->Cost : false;
        return new OtapiMoney($value);
    }
}