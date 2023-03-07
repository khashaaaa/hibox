<?php

class OtapiMoney extends BaseOtapiType{
    /**
    * @return string
    */
    public function GetSignAttribute(){
        $attributes = $this->xmlData && $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Sign']) ? (string)$attributes['Sign'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return string
    */
    public function GetCodeAttribute(){
        $attributes = $this->xmlData && $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Code']) ? (string)$attributes['Code'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return float
    */
    public function GetValue(){
        return (float)$this->asString();
    }
}