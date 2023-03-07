<?php

class OtapiDataListOfCurrencySynchronizationMode extends BaseOtapiType{
    /**
     * @return OtapiArrayOfCurrencySynchronizationMode
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfCurrencySynchronizationMode($value);
    }
}