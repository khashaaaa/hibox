<?php

class OtapiShowcaseCurrencyRatesAnswer extends OtapiAnswer{
    /**
     * @return OtapiShowcaseCurrencyRates
     */
    public function GetCurrencyRates(){
        $value = isset($this->xmlData->CurrencyRates) ? $this->xmlData->CurrencyRates : false;
        return new OtapiShowcaseCurrencyRates($value);
    }
}