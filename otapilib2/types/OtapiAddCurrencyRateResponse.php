<?php

class OtapiAddCurrencyRateResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetAddCurrencyRateResult(){
        $value = isset($this->xmlData->AddCurrencyRateResult) ? $this->xmlData->AddCurrencyRateResult : false;
        return new VoidOtapiAnswer($value);
    }
}