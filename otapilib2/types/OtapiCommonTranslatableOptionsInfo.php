<?php

class OtapiCommonTranslatableOptionsInfo extends BaseOtapiType{
    /**
     * @return OtapiDataListOfProviderInfo
     */
    public function GetProviders(){
        $value = isset($this->xmlData->Providers) ? $this->xmlData->Providers : false;
        return new OtapiDataListOfProviderInfo($value);
    }
    /**
     * @return OtapiDataListOfCountryInfo
     */
    public function GetDeliveryCountries(){
        $value = isset($this->xmlData->DeliveryCountries) ? $this->xmlData->DeliveryCountries : false;
        return new OtapiDataListOfCountryInfo($value);
    }
    /**
     * @return OtapiDataListOfAuthenticationSystemInfo
     */
    public function GetExternalAuthentications(){
        $value = isset($this->xmlData->ExternalAuthentications) ? $this->xmlData->ExternalAuthentications : false;
        return new OtapiDataListOfAuthenticationSystemInfo($value);
    }
    public function GetUserProfileFields(){
        $value = isset($this->xmlData->UserProfileFields) ? $this->xmlData->UserProfileFields : false;
        return new OtapiDataListOfUserProfileFieldSettings($value);
    }
    /**
    * @return string
    */
    public function GetLanguageAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Language']) ? (string)$attributes['Language'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}