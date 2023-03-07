<?php

class OtapiCurrencyRateListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfCurrencyRate
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfCurrencyRate($value);
    }
}