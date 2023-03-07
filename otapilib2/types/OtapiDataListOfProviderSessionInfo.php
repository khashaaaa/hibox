<?php

class OtapiDataListOfProviderSessionInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfProviderSessionInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfProviderSessionInfo($value);
    }
}