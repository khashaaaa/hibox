<?php

class OtapiVendorInfoListAnswer extends OtapiAnswer{
    /**
     * @return DataListOfOtapiVendorInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataListOfOtapiVendorInfo($value);
    }
}