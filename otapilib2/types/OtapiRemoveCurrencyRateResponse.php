<?php

class OtapiRemoveCurrencyRateResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetRemoveCurrencyRateResult(){
        $value = isset($this->xmlData->RemoveCurrencyRateResult) ? $this->xmlData->RemoveCurrencyRateResult : false;
        return new VoidOtapiAnswer($value);
    }
}