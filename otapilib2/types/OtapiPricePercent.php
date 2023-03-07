<?php

class OtapiPricePercent extends BaseOtapiType{
    /**
    * @return string
    */
    public function GetCurrencyCodeAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['CurrencyCode']) ? (string)$attributes['CurrencyCode'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}