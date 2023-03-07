<?php

class OtapiCurrencyRate extends BaseOtapiType{
    /**
    * @return string
    */
    public function GetFirstCodeAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['FirstCode']) ? (string)$attributes['FirstCode'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return string
    */
    public function GetSecondCodeAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['SecondCode']) ? (string)$attributes['SecondCode'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}