<?php

class OtapiUserSettings extends BaseOtapiType{
    /**
     * @return boolean
     */
    public function IsPhoneRegistrationAllowed(){
        $value = isset($this->xmlData->IsPhoneRegistrationAllowed) ? (string)$this->xmlData->IsPhoneRegistrationAllowed : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiMetaInfo
     */
    public function GetMetaInfo(){
        $value = isset($this->xmlData->MetaInfo) ? $this->xmlData->MetaInfo : false;
        return new OtapiMetaInfo($value);
    }
}