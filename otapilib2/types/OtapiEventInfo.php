<?php

class OtapiEventInfo extends BaseOtapiType{
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
    public function GetDescription(){
        $value = isset($this->xmlData->Description) ? (string)$this->xmlData->Description : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsEnabled(){
        $value = isset($this->xmlData->IsEnabled) ? (string)$this->xmlData->IsEnabled : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}