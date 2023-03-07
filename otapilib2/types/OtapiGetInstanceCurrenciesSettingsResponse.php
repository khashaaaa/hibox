<?php

class OtapiGetInstanceCurrenciesSettingsResponse extends BaseOtapiType{
    /**
     * @return OtapiCurrencySettingsAnswer
     */
    public function GetGetInstanceCurrenciesSettingsResult(){
        $value = isset($this->xmlData->GetInstanceCurrenciesSettingsResult) ? $this->xmlData->GetInstanceCurrenciesSettingsResult : false;
        return new OtapiCurrencySettingsAnswer($value);
    }
}