<?php

class OtapiLogEntryInfo extends OtapiLogEntryBaseData{
    /**
     * @return string
     */
    public function GetInstanceUserLogin(){
        $value = isset($this->xmlData->InstanceUserLogin) ? (string)$this->xmlData->InstanceUserLogin : false;
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
     * @return dateTime
     */
    public function GetDateTime(){
        $value = isset($this->xmlData->DateTime) ? (string)$this->xmlData->DateTime : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}