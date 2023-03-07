<?php

class OtapiCurrencySynchronizationModeListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfCurrencySynchronizationMode
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfCurrencySynchronizationMode($value);
    }
}