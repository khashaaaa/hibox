<?php

class OtapiDataListOfUserProfileInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfUserProfileInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfUserProfileInfo($value);
    }
}