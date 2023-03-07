<?php

class OtapiUserProfileUpdateData extends BaseOtapiType{
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
    public function GetCountryCode(){
        $value = isset($this->xmlData->CountryCode) ? (string)$this->xmlData->CountryCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetRegion(){
        $value = isset($this->xmlData->Region) ? (string)$this->xmlData->Region : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCity(){
        $value = isset($this->xmlData->City) ? (string)$this->xmlData->City : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetAddress(){
        $value = isset($this->xmlData->Address) ? (string)$this->xmlData->Address : false;
        $propertyType = 'string';
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
    /**
     * @return string
     */
    public function GetPostalCode(){
        $value = isset($this->xmlData->PostalCode) ? (string)$this->xmlData->PostalCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPassportNumber(){
        $value = isset($this->xmlData->PassportNumber) ? (string)$this->xmlData->PassportNumber : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetRegistrationAddress(){
        $value = isset($this->xmlData->RegistrationAddress) ? (string)$this->xmlData->RegistrationAddress : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}