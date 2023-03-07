<?php

class OtapiBasePrice extends OtapiAbstractCustomizablePrice{
    /**
     * @return decimal
     */
    public function GetOriginalPrice(){
        $value = isset($this->xmlData->OriginalPrice) ? (string)$this->xmlData->OriginalPrice : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetMarginPrice(){
        $value = isset($this->xmlData->MarginPrice) ? (string)$this->xmlData->MarginPrice : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetOriginalCurrencyCode(){
        $value = isset($this->xmlData->OriginalCurrencyCode) ? (string)$this->xmlData->OriginalCurrencyCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiInstanceListOfMoney
     */
    public function GetConvertedPriceList(){
        $value = isset($this->xmlData->ConvertedPriceList) ? $this->xmlData->ConvertedPriceList : false;
        return new OtapiInstanceListOfMoney($value);
    }
}