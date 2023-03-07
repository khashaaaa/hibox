<?php

class OtapiDataListOfUserProfileFieldSettings extends BaseOtapiType{
    /**
     * @return OtapiArrayOfUserProfileFieldSettings
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfUserProfileFieldSettings($value);
    }
}