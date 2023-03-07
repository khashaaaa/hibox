<?php

class OtapiValuedConfigurator extends BaseOtapiType{
    /**
    * @return string
    */
    public function GetPidAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Pid']) ? (string)$attributes['Pid'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return string
    */
    public function GetVidAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Vid']) ? (string)$attributes['Vid'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}