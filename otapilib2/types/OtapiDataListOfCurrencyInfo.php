<?php

class OtapiDataListOfCurrencyInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfCurrencyInfo1
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfCurrencyInfo1($value);
    }
}