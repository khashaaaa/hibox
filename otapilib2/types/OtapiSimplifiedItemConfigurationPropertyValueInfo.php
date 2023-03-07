<?php

class OtapiSimplifiedItemConfigurationPropertyValueInfo extends BaseOtapiType{
    /**
    * @return string
    */
    public function GetIdAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Id']) ? (string)$attributes['Id'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return boolean
    */
    public function DisabledAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Disabled']) ? (string)$attributes['Disabled'] : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return boolean
    */
    public function SelectedAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Selected']) ? (string)$attributes['Selected'] : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}