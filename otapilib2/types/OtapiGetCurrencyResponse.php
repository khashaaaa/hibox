<?php

class OtapiGetCurrencyResponse extends BaseOtapiType{
    /**
     * @return OtapiCurrencyInfoAnswer
     */
    public function GetGetCurrencyResult(){
        $value = isset($this->xmlData->GetCurrencyResult) ? $this->xmlData->GetCurrencyResult : false;
        return new OtapiCurrencyInfoAnswer($value);
    }
}