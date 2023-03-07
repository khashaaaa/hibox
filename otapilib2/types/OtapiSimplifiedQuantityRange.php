<?php

class OtapiSimplifiedQuantityRange extends OtapiSimplifiedItemPriceInfo{
    /**
    * @return long
    */
    public function GetMinQuantityAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['MinQuantity']) ? (string)$attributes['MinQuantity'] : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return long
    */
    public function GetMaxQuantityAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['MaxQuantity']) ? (string)$attributes['MaxQuantity'] : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}