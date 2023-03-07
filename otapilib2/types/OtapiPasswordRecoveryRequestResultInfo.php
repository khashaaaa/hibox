<?php

class OtapiPasswordRecoveryRequestResultInfo extends BaseOtapiType{
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
    public function GetConfirmationCode(){
        $value = isset($this->xmlData->ConfirmationCode) ? (string)$this->xmlData->ConfirmationCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}