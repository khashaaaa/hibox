<?php

class OtapiProviderSearchFeatureInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetName(){
        $value = isset($this->xmlData->Name) ? (string)$this->xmlData->Name : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function CanBeTrue(){
        $value = isset($this->xmlData->CanBeTrue) ? (string)$this->xmlData->CanBeTrue : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function CanBeFalse(){
        $value = isset($this->xmlData->CanBeFalse) ? (string)$this->xmlData->CanBeFalse : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDisplayName(){
        $value = isset($this->xmlData->DisplayName) ? (string)$this->xmlData->DisplayName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDisplayNameForTrue(){
        $value = isset($this->xmlData->DisplayNameForTrue) ? (string)$this->xmlData->DisplayNameForTrue : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDisplayNameForFalse(){
        $value = isset($this->xmlData->DisplayNameForFalse) ? (string)$this->xmlData->DisplayNameForFalse : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}