<?php

class OtapiDataListOfCurrencyRate extends BaseOtapiType{
    /**
     * @return OtapiArrayOfCurrencyRate1
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfCurrencyRate1($value);
    }
}