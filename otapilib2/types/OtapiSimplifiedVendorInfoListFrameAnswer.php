<?php

class OtapiSimplifiedVendorInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataSubListOfSimplifiedVendorInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataSubListOfSimplifiedVendorInfo($value);
    }
}