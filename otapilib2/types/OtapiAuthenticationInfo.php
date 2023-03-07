<?php

class OtapiAuthenticationInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetRedirectUrl(){
        $value = isset($this->xmlData->RedirectUrl) ? (string)$this->xmlData->RedirectUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}