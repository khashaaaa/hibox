<?php

class OtapiCurrencySettings extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetSyncMode(){
        $value = isset($this->xmlData->SyncMode) ? (string)$this->xmlData->SyncMode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetMarginRate(){
        $value = isset($this->xmlData->MarginRate) ? (string)$this->xmlData->MarginRate : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetInternalCurrencyCode(){
        $value = isset($this->xmlData->InternalCurrencyCode) ? (string)$this->xmlData->InternalCurrencyCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfCurrencyRate
     */
    public function GetCurrencyRateList(){
        $value = isset($this->xmlData->CurrencyRateList) ? $this->xmlData->CurrencyRateList : false;
        return new OtapiArrayOfCurrencyRate($value);
    }
    /**
     * @return OtapiArrayOfOrderedCurrency
     */
    public function GetCurrenciesDisplayingOrder(){
        $value = isset($this->xmlData->CurrenciesDisplayingOrder) ? $this->xmlData->CurrenciesDisplayingOrder : false;
        return new OtapiArrayOfOrderedCurrency($value);
    }
}