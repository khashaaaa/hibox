<?php

class OtapiShowcaseCurrencyRates extends BaseOtapiType{
    /**
     * @return OtapiCurrencyInformation
     */
    public function GetRuble(){
        $value = isset($this->xmlData->Ruble) ? $this->xmlData->Ruble : false;
        return new OtapiCurrencyInformation($value);
    }
    /**
     * @return OtapiCurrencyInformation
     */
    public function GetDollar(){
        $value = isset($this->xmlData->Dollar) ? $this->xmlData->Dollar : false;
        return new OtapiCurrencyInformation($value);
    }
    /**
     * @return OtapiCurrencyInformation
     */
    public function GetYuan(){
        $value = isset($this->xmlData->Yuan) ? $this->xmlData->Yuan : false;
        return new OtapiCurrencyInformation($value);
    }
}