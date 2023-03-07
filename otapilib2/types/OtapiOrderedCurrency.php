<?php

class OtapiOrderedCurrency extends BaseOtapiType{
    /**
    * @return string
    */
    public function GetCodeAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Code']) ? (string)$attributes['Code'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return int
    */
    public function GetOrderAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Order']) ? (string)$attributes['Order'] : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}