<?php

class OtapiGetCurrencyInstanceListResponse extends BaseOtapiType{
    /**
     * @return OtapiInstanceListOfCurrencyInfoAnswer
     */
    public function GetGetCurrencyInstanceListResult(){
        $value = isset($this->xmlData->GetCurrencyInstanceListResult) ? $this->xmlData->GetCurrencyInstanceListResult : false;
        return new OtapiInstanceListOfCurrencyInfoAnswer($value);
    }
}