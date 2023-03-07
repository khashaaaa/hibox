<?php

class OtapiSimplifiedPrice extends BaseOtapiType{
    /**
    * @return decimal
    */
    public function GetMinAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Min']) ? (string)$attributes['Min'] : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return decimal
    */
    public function GetMaxAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Max']) ? (string)$attributes['Max'] : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}