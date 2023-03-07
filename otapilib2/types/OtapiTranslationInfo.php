<?php

class OtapiTranslationInfo extends BaseOtapiType{
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
    public function GetSystemTranslation(){
        $value = isset($this->xmlData->SystemTranslation) ? (string)$this->xmlData->SystemTranslation : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCustomTranslation(){
        $value = isset($this->xmlData->CustomTranslation) ? (string)$this->xmlData->CustomTranslation : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}