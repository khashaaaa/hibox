<?php

class OtapiGetCurrencyListResponse extends BaseOtapiType{
    /**
     * @return OtapiCurrencyInfoListAnswer
     */
    public function GetGetCurrencyListResult(){
        $value = isset($this->xmlData->GetCurrencyListResult) ? $this->xmlData->GetCurrencyListResult : false;
        return new OtapiCurrencyInfoListAnswer($value);
    }
}