<?php

class OtapiPackageTrackingInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiPackageTrackingInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiPackageTrackingInfo($value);
    }
}