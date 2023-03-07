<?php

class OtapiFeatureTypeInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetName(){
        $value = isset($this->xmlData->Name) ? (string)$this->xmlData->Name : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiMoney
     */
    public function GetRentPrice(){
        $value = isset($this->xmlData->RentPrice) ? $this->xmlData->RentPrice : false;
        return new OtapiMoney($value);
    }
    /**
     * @return boolean
     */
    public function IsDisabledByPayerStatus(){
        $value = isset($this->xmlData->IsDisabledByPayerStatus) ? (string)$this->xmlData->IsDisabledByPayerStatus : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}