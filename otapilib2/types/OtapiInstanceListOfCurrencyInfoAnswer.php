<?php

class OtapiInstanceListOfCurrencyInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiInstanceListOfCurrencyInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiInstanceListOfCurrencyInfo($value);
    }
}