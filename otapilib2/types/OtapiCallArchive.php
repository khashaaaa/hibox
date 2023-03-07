<?php

class OtapiCallArchive extends BaseOtapiType{
    /**
     * @return OtapiArchive
     */
    public function GetTotalCallsArchive(){
        $value = isset($this->xmlData->TotalCallsArchive) ? $this->xmlData->TotalCallsArchive : false;
        return new OtapiArchive($value);
    }
    /**
     * @return OtapiArchive
     */
    public function GetPaidCallsArchive(){
        $value = isset($this->xmlData->PaidCallsArchive) ? $this->xmlData->PaidCallsArchive : false;
        return new OtapiArchive($value);
    }
}