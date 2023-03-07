<?php

class OtapiPackageTotalCostPerCurrency extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetCurrencyCode(){
        $value = isset($this->xmlData->CurrencyCode) ? (string)$this->xmlData->CurrencyCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetTotalPrice(){
        $value = isset($this->xmlData->TotalPrice) ? (string)$this->xmlData->TotalPrice : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetTotalAdditionalPrice(){
        $value = isset($this->xmlData->TotalAdditionalPrice) ? (string)$this->xmlData->TotalAdditionalPrice : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}