<?php

class OtapiCommonRegistrationOptionsInfo extends BaseOtapiType{
    /**
     * @return boolean
     */
    public function IsSimpleRegistrationAllowed(){
        $value = isset($this->xmlData->IsSimpleRegistrationAllowed) ? (string)$this->xmlData->IsSimpleRegistrationAllowed : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsPhoneRegistrationAllowed(){
        $value = isset($this->xmlData->IsPhoneRegistrationAllowed) ? (string)$this->xmlData->IsPhoneRegistrationAllowed : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}