<?php

class OtapiAuthenticationSystemInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? (string)$this->xmlData->Id : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetName(){
        $value = isset($this->xmlData->Name) ? (string)$this->xmlData->Name : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetIconUrl(){
        $value = isset($this->xmlData->IconUrl) ? (string)$this->xmlData->IconUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}