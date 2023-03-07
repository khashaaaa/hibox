<?php

class OtapiGetTariffChangeHistoryResponse extends BaseOtapiType{
    /**
     * @return OtapiTariffChangeHistoryAnswer
     */
    public function GetGetTariffChangeHistoryResult(){
        $value = isset($this->xmlData->GetTariffChangeHistoryResult) ? $this->xmlData->GetTariffChangeHistoryResult : false;
        return new OtapiTariffChangeHistoryAnswer($value);
    }
}