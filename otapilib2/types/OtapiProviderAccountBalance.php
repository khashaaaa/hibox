<?php

class OtapiProviderAccountBalance extends BaseOtapiType{
    /**
     * @return decimal
     */
    public function GetAmount(){
        $value = isset($this->xmlData->Amount) ? (string)$this->xmlData->Amount : false;
        $propertyType = 'decimal';
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
}