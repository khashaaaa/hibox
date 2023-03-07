<?php

class OtapiAdditionalVendorInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiAdditionalVendorInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiAdditionalVendorInfo($value);
    }
}