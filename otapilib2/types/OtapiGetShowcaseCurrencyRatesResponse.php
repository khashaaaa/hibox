<?php

class OtapiGetShowcaseCurrencyRatesResponse extends BaseOtapiType{
    /**
     * @return OtapiShowcaseCurrencyRatesAnswer
     */
    public function GetGetShowcaseCurrencyRatesResult(){
        $value = isset($this->xmlData->GetShowcaseCurrencyRatesResult) ? $this->xmlData->GetShowcaseCurrencyRatesResult : false;
        return new OtapiShowcaseCurrencyRatesAnswer($value);
    }
}