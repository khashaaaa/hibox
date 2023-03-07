<?php

class OtapiSocialNetworkOptionsData extends OtapiAbstractMetaListItem{
    /**
     * @return string
     */
    public function GetCommonShareWidget(){
        $value = isset($this->xmlData->CommonShareWidget) ? (string)$this->xmlData->CommonShareWidget : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiMetaListOfSocialNetworkData
     */
    public function GetMainInfo(){
        $value = isset($this->xmlData->MainInfo) ? $this->xmlData->MainInfo : false;
        return new OtapiMetaListOfSocialNetworkData($value);
    }
    /**
     * @return OtapiValueListOfAuthenticationSystemType
     */
    public function GetExternalAuthentications(){
        $value = isset($this->xmlData->ExternalAuthentications) ? $this->xmlData->ExternalAuthentications : false;
        return new OtapiValueListOfAuthenticationSystemType($value);
    }
}