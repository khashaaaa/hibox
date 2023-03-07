<?php

class OtapiVendorInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return DataSubListOfOtapiVendorInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataSubListOfOtapiVendorInfo($value);
    }
}