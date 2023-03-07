<?php

class OtapiDataListOfAuthenticationSystemInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfAuthenticationSystemInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfAuthenticationSystemInfo($value);
    }
}