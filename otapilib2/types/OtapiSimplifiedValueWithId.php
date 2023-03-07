<?php

class OtapiSimplifiedValueWithId extends BaseOtapiType{
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
     * @return string
     */
    public function GetDisplayNameAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['DisplayName']) ? (string)$attributes['DisplayName'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return SimplifiedDisplayType
     */
    public function GetDisplayTypeAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['DisplayType']) ? (string)$attributes['DisplayType'] : false;
        $propertyType = 'SimplifiedDisplayType';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}