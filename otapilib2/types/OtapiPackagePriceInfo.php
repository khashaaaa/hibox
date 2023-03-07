<?php

class OtapiPackagePriceInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? (string)$this->xmlData->Id : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetPriceInternal(){
        $value = isset($this->xmlData->PriceInternal) ? (string)$this->xmlData->PriceInternal : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetAdditionalPriceInternal(){
        $value = isset($this->xmlData->AdditionalPriceInternal) ? (string)$this->xmlData->AdditionalPriceInternal : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetAdditionalPrice(){
        $value = isset($this->xmlData->AdditionalPrice) ? (string)$this->xmlData->AdditionalPrice : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetPrice(){
        $value = isset($this->xmlData->Price) ? (string)$this->xmlData->Price : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPriceCurrencyCode(){
        $value = isset($this->xmlData->PriceCurrencyCode) ? (string)$this->xmlData->PriceCurrencyCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return dateTime
     */
    public function GetPriceUpdateDate(){
        $value = isset($this->xmlData->PriceUpdateDate) ? (string)$this->xmlData->PriceUpdateDate : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}