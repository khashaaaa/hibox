<?php

class OtapiValidateUserProfile extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetInstanceKey(){
        $value = isset($this->xmlData->instanceKey) ? (string)$this->xmlData->instanceKey : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLanguage(){
        $value = isset($this->xmlData->language) ? (string)$this->xmlData->language : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetValidateData(){
        $value = isset($this->xmlData->validateData) ? (string)$this->xmlData->validateData : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}