<?php

class OtapiSimplifiedId extends BaseOtapiType{
    /**
    * @return string
    */
    public function GetDisplayNameAttribute(){
        $attributes = $this->xmlData && $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['DisplayName']) ? (string)$attributes['DisplayName'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}