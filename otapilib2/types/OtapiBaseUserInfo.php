<?php

class OtapiBaseUserInfo extends BaseOtapiType{
    /**
     * @return OtapiUserId
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? $this->xmlData->Id : false;
        return new OtapiUserId($value);
    }
    /**
     * @return string
     */
    public function GetLogin(){
        $value = isset($this->xmlData->Login) ? (string)$this->xmlData->Login : false;
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
    public function IsActive(){
        $value = isset($this->xmlData->IsActive) ? (string)$this->xmlData->IsActive : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetFirstName(){
        $value = isset($this->xmlData->FirstName) ? (string)$this->xmlData->FirstName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLastName(){
        $value = isset($this->xmlData->LastName) ? (string)$this->xmlData->LastName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetMiddleName(){
        $value = isset($this->xmlData->MiddleName) ? (string)$this->xmlData->MiddleName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPersonalAccountId(){
        $value = isset($this->xmlData->PersonalAccountId) ? (string)$this->xmlData->PersonalAccountId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsEmailVerified(){
        $value = isset($this->xmlData->IsEmailVerified) ? (string)$this->xmlData->IsEmailVerified : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPhone(){
        $value = isset($this->xmlData->Phone) ? (string)$this->xmlData->Phone : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}