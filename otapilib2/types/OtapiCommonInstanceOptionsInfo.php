<?php

class OtapiCommonInstanceOptionsInfo extends BaseOtapiType{
    /**
     * @return OtapiCommonRegistrationOptionsInfo
     */
    public function GetRegistration(){
        $value = isset($this->xmlData->Registration) ? $this->xmlData->Registration : false;
        return new OtapiCommonRegistrationOptionsInfo($value);
    }
    /**
     * @return OtapiCommonOrderOptionsInfo
     */
    public function GetOrder(){
        $value = isset($this->xmlData->Order) ? $this->xmlData->Order : false;
        return new OtapiCommonOrderOptionsInfo($value);
    }
    /**
     * @return OtapiCommonUserProfileOptionsInfo
     */
    public function GetUserProfile(){
        $value = isset($this->xmlData->UserProfile) ? $this->xmlData->UserProfile : false;
        return new OtapiCommonUserProfileOptionsInfo($value);
    }
    /**
     * @return OtapiCommonInstanceKeyOptionsInfo
     */
    public function GetInstanceKey(){
        $value = isset($this->xmlData->InstanceKey) ? $this->xmlData->InstanceKey : false;
        return new OtapiCommonInstanceKeyOptionsInfo($value);
    }
    /**
     * @return OtapiCommonShowcaseOptionsInfo
     */
    public function GetShowcase(){
        $value = isset($this->xmlData->Showcase) ? $this->xmlData->Showcase : false;
        return new OtapiCommonShowcaseOptionsInfo($value);
    }
    /**
     * @return OtapiCommonRatingListOptionsInfo
     */
    public function GetRatingLists(){
        $value = isset($this->xmlData->RatingLists) ? $this->xmlData->RatingLists : false;
        return new OtapiCommonRatingListOptionsInfo($value);
    }
    /**
     * @return OtapiSocialNetworkOptionsInfo
     */
    public function GetSocialNetworks(){
        $value = isset($this->xmlData->SocialNetworks) ? $this->xmlData->SocialNetworks : false;
        return new OtapiSocialNetworkOptionsInfo($value);
    }
    /**
     * @return OtapiInstanceListOfCurrencyInfo
     */
    public function GetCurrencies(){
        $value = isset($this->xmlData->Currencies) ? $this->xmlData->Currencies : false;
        return new OtapiInstanceListOfCurrencyInfo($value);
    }
    /**
     * @return OtapiDataListOfLanguageInfo
     */
    public function GetLanguages(){
        $value = isset($this->xmlData->Languages) ? $this->xmlData->Languages : false;
        return new OtapiDataListOfLanguageInfo($value);
    }
    /**
     * @return OtapiDataListOfString
     */
    public function GetFeatures(){
        $value = isset($this->xmlData->Features) ? $this->xmlData->Features : false;
        return new OtapiDataListOfString($value);
    }
    /**
     * @return DataListOfOtapiCommonTranslatableOptionsInfo
     */
    public function GetTranslatableOptions(){
        $value = isset($this->xmlData->TranslatableOptions) ? $this->xmlData->TranslatableOptions : false;
        return new DataListOfOtapiCommonTranslatableOptionsInfo($value);
    }
    /**
     * @return OtapiApplicationDesignSettings
     */
    public function GetDesignOptions(){
        $value = isset($this->xmlData->DesignOptions) ? $this->xmlData->DesignOptions : false;
        return new OtapiApplicationDesignSettings($value);
    }
}