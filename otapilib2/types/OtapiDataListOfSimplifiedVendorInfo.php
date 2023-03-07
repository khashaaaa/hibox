<?php

class OtapiDataListOfSimplifiedVendorInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfSimplifiedVendorInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfSimplifiedVendorInfo($value);
    }
}