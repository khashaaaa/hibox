<?php

class OtapiAbstractMetaListItem extends BaseOtapiType{
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
    * @return MetaListUpdateAction
    */
    public function GetActionAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Action']) ? (string)$attributes['Action'] : false;
        $propertyType = 'MetaListUpdateAction';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}