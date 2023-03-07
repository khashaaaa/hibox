<?php

class OtapiDataListOfPackageTrackingCheckpoint extends BaseOtapiType{
    /**
     * @return OtapiArrayOfPackageTrackingCheckpoint
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfPackageTrackingCheckpoint($value);
    }
}