<?php

class OtapiDataListOfCityInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfCityInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfCityInfo($value);
    }
}