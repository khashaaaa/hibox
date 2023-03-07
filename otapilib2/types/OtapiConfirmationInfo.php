<?php

class OtapiConfirmationInfo extends BaseOtapiType{
    /**
     * @return OtapiUserId
     */
    public function GetUserId(){
        $value = isset($this->xmlData->UserId) ? $this->xmlData->UserId : false;
        return new OtapiUserId($value);
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
     * @return string
     */
    public function GetPhone(){
        $value = isset($this->xmlData->Phone) ? (string)$this->xmlData->Phone : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsEmailVerificationUsed(){
        $value = isset($this->xmlData->IsEmailVerificationUsed) ? (string)$this->xmlData->IsEmailVerificationUsed : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetEmailConfirmationCode(){
        $value = isset($this->xmlData->EmailConfirmationCode) ? (string)$this->xmlData->EmailConfirmationCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiSessionId
     */
    public function GetSessionId(){
        $value = isset($this->xmlData->SessionId) ? $this->xmlData->SessionId : false;
        return new OtapiSessionId($value);
    }
    /**
     * @return boolean
     */
    public function IsPhoneVerificationUsed(){
        $value = isset($this->xmlData->IsPhoneVerificationUsed) ? (string)$this->xmlData->IsPhoneVerificationUsed : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPhoneConfirmationCode(){
        $value = isset($this->xmlData->PhoneConfirmationCode) ? (string)$this->xmlData->PhoneConfirmationCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}