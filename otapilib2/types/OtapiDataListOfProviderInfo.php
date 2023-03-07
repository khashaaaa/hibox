<?php

class OtapiDataListOfProviderInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfProviderInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfProviderInfo($value);
    }
}