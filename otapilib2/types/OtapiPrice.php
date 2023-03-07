<?php

class OtapiPrice extends OtapiBasePrice{
    /**
     * @return string
     */
    public function GetConvertedPrice(){
        $value = isset($this->xmlData->ConvertedPrice) ? (string)$this->xmlData->ConvertedPrice : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetConvertedPriceWithoutSign(){
        $value = isset($this->xmlData->ConvertedPriceWithoutSign) ? (string)$this->xmlData->ConvertedPriceWithoutSign : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCurrencySign(){
        $value = isset($this->xmlData->CurrencySign) ? (string)$this->xmlData->CurrencySign : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCurrencyName(){
        $value = isset($this->xmlData->CurrencyName) ? (string)$this->xmlData->CurrencyName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsDeliverable(){
        $value = isset($this->xmlData->IsDeliverable) ? (string)$this->xmlData->IsDeliverable : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiBasePrice
     */
    public function GetDeliveryPrice(){
        $value = isset($this->xmlData->DeliveryPrice) ? $this->xmlData->DeliveryPrice : false;
        return new OtapiBasePrice($value);
    }
    /**
     * @return OtapiBasePrice
     */
    public function GetOneItemDeliveryPrice(){
        $value = isset($this->xmlData->OneItemDeliveryPrice) ? $this->xmlData->OneItemDeliveryPrice : false;
        return new OtapiBasePrice($value);
    }
    /**
     * @return OtapiBasePrice
     */
    public function GetPriceWithoutDelivery(){
        $value = isset($this->xmlData->PriceWithoutDelivery) ? $this->xmlData->PriceWithoutDelivery : false;
        return new OtapiBasePrice($value);
    }
}