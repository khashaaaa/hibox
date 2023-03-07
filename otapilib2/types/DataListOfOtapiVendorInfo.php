<?php

class DataListOfOtapiVendorInfo extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiVendorInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiVendorInfo($value);
    }
}