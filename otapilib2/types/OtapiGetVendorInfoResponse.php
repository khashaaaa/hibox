<?php

class OtapiGetVendorInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiVendorInfoAnswer
     */
    public function GetGetVendorInfoResult(){
        $value = isset($this->xmlData->GetVendorInfoResult) ? $this->xmlData->GetVendorInfoResult : false;
        return new OtapiVendorInfoAnswer($value);
    }
}