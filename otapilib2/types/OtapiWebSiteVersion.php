<?php

class OtapiWebSiteVersion extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetNumber(){
        $value = isset($this->xmlData->Number) ? (string)$this->xmlData->Number : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return dateTime
     */
    public function GetLastUpdateTime(){
        $value = isset($this->xmlData->LastUpdateTime) ? (string)$this->xmlData->LastUpdateTime : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function HasError(){
        $value = isset($this->xmlData->HasError) ? (string)$this->xmlData->HasError : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetErrorMessage(){
        $value = isset($this->xmlData->ErrorMessage) ? (string)$this->xmlData->ErrorMessage : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}