<?php

class OtapiPasswordRecoveryConfirmationResultInfo extends BaseOtapiType{
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
    public function GetLogin(){
        $value = isset($this->xmlData->Login) ? (string)$this->xmlData->Login : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPassword(){
        $value = isset($this->xmlData->Password) ? (string)$this->xmlData->Password : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}