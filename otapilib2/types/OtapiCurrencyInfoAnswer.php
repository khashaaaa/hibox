<?php

class OtapiCurrencyInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiCurrencyInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiCurrencyInfo($value);
    }
}