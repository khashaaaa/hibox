<?php

class OtapiGetInstanceCurrencyRateListResponse extends BaseOtapiType{
    /**
     * @return OtapiCurrencyRateListAnswer
     */
    public function GetGetInstanceCurrencyRateListResult(){
        $value = isset($this->xmlData->GetInstanceCurrencyRateListResult) ? $this->xmlData->GetInstanceCurrencyRateListResult : false;
        return new OtapiCurrencyRateListAnswer($value);
    }
}