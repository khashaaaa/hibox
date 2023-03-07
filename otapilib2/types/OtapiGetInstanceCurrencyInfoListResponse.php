<?php

class OtapiGetInstanceCurrencyInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiInstanceListOfCurrencyInfoAnswer
     */
    public function GetGetInstanceCurrencyInfoListResult(){
        $value = isset($this->xmlData->GetInstanceCurrencyInfoListResult) ? $this->xmlData->GetInstanceCurrencyInfoListResult : false;
        return new OtapiInstanceListOfCurrencyInfoAnswer($value);
    }
}