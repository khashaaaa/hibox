<?php

class OtapiSocialNetworkOptionsInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetCommonShareWidget(){
        $value = isset($this->xmlData->CommonShareWidget) ? (string)$this->xmlData->CommonShareWidget : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiDataListOfSocialNetworkInfo
     */
    public function GetMainInfo(){
        $value = isset($this->xmlData->MainInfo) ? $this->xmlData->MainInfo : false;
        return new OtapiDataListOfSocialNetworkInfo($value);
    }
    /**
     * @return OtapiDataListOfAuthenticationSystemInfo
     */
    public function GetExternalAuthentications(){
        $value = isset($this->xmlData->ExternalAuthentications) ? $this->xmlData->ExternalAuthentications : false;
        return new OtapiDataListOfAuthenticationSystemInfo($value);
    }
}