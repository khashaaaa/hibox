<?php

class OtapiVendorInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiVendorInfo
     */
    public function GetVendorInfo(){
        $value = isset($this->xmlData->VendorInfo) ? $this->xmlData->VendorInfo : false;
        return new OtapiVendorInfo($value);
    }
}