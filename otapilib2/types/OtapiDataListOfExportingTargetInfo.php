<?php

class OtapiDataListOfExportingTargetInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfExportingTargetInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfExportingTargetInfo($value);
    }
}