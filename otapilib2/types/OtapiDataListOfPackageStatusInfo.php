<?php

class OtapiDataListOfPackageStatusInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfPackageStatusInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfPackageStatusInfo($value);
    }
}