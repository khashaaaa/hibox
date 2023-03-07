<?php

class OtapiDataListOfCountryInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfCountryInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfCountryInfo($value);
    }
}