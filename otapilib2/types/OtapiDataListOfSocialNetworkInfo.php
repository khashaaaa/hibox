<?php

class OtapiDataListOfSocialNetworkInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfSocialNetworkInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfSocialNetworkInfo($value);
    }
}