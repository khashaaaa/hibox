<?php

class OtapiCurrencyInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfCurrencyInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfCurrencyInfo($value);
    }
}