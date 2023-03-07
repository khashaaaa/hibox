<?php

class OtapiSimplifiedItemPropertyValue extends BaseOtapiType{
    /**
     * @return OtapiSimplifiedPicture
     */
    public function GetPicture(){
        $value = isset($this->xmlData->Picture) ? $this->xmlData->Picture : false;
        return new OtapiSimplifiedPicture($value);
    }
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
}