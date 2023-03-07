<?php

class OtapiInstanceListOfCurrencyInfo extends BaseOtapiType{
    /**
     * @return OtapiCurrencyInfo
     */
    public function GetInternal(){
        $value = isset($this->xmlData->Internal) ? $this->xmlData->Internal : false;
        return new OtapiCurrencyInfo($value);
    }
    /**
     * @return OtapiArrayOfCurrencyInfo
     */
    public function GetDisplayedMoneys(){
        $value = isset($this->xmlData->DisplayedMoneys) ? $this->xmlData->DisplayedMoneys : false;
        return new OtapiArrayOfCurrencyInfo($value);
    }
}