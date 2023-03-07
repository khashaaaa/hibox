<?php

class OtapiOrderTotalCostPerCurrency extends BaseOtapiType{
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
    public function GetTotalCost(){
        $value = isset($this->xmlData->TotalCost) ? (string)$this->xmlData->TotalCost : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetTotalCostInInternalCurrency(){
        $value = isset($this->xmlData->TotalCostInInternalCurrency) ? (string)$this->xmlData->TotalCostInInternalCurrency : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}