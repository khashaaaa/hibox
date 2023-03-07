<?php

class OtapiInstanceBaseInfo extends BaseOtapiType{
    /**
     * @return int
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? (string)$this->xmlData->Id : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetInstanceKey(){
        $value = isset($this->xmlData->InstanceKey) ? (string)$this->xmlData->InstanceKey : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetContactInfo(){
        $value = isset($this->xmlData->ContactInfo) ? (string)$this->xmlData->ContactInfo : false;
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
     * @return dateTime
     */
    public function GetLastActivityTime(){
        $value = isset($this->xmlData->LastActivityTime) ? (string)$this->xmlData->LastActivityTime : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsOnMarket(){
        $value = isset($this->xmlData->IsOnMarket) ? (string)$this->xmlData->IsOnMarket : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return dateTime
     */
    public function GetCreationDate(){
        $value = isset($this->xmlData->CreationDate) ? (string)$this->xmlData->CreationDate : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsEmailConfirmationUsed(){
        $value = isset($this->xmlData->IsEmailConfirmationUsed) ? (string)$this->xmlData->IsEmailConfirmationUsed : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsTest(){
        $value = isset($this->xmlData->IsTest) ? (string)$this->xmlData->IsTest : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsBanned(){
        $value = isset($this->xmlData->IsBanned) ? (string)$this->xmlData->IsBanned : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetWebSite(){
        $value = isset($this->xmlData->WebSite) ? (string)$this->xmlData->WebSite : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetEmail(){
        $value = isset($this->xmlData->Email) ? (string)$this->xmlData->Email : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsInternal(){
        $value = isset($this->xmlData->IsInternal) ? (string)$this->xmlData->IsInternal : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiWebSiteVersion
     */
    public function GetVersionInfo(){
        $value = isset($this->xmlData->VersionInfo) ? $this->xmlData->VersionInfo : false;
        return new OtapiWebSiteVersion($value);
    }
}