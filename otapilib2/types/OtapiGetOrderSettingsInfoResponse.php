<?php

class OtapiGetOrderSettingsInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiOrderSettingsInfoAnswer
     */
    public function GetGetOrderSettingsInfoResult(){
        $value = isset($this->xmlData->GetOrderSettingsInfoResult) ? $this->xmlData->GetOrderSettingsInfoResult : false;
        return new OtapiOrderSettingsInfoAnswer($value);
    }
}