<?php

class OtapiDataListOfProviderSearchMethodInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfProviderSearchMethodInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfProviderSearchMethodInfo($value);
    }
}