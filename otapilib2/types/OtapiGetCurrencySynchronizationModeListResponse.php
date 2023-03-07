<?php

class OtapiGetCurrencySynchronizationModeListResponse extends BaseOtapiType{
    /**
     * @return OtapiCurrencySynchronizationModeListAnswer
     */
    public function GetGetCurrencySynchronizationModeListResult(){
        $value = isset($this->xmlData->GetCurrencySynchronizationModeListResult) ? $this->xmlData->GetCurrencySynchronizationModeListResult : false;
        return new OtapiCurrencySynchronizationModeListAnswer($value);
    }
}