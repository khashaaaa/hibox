<?php

class OtapiUpdateInstanceCurrenciesSettingsResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateInstanceCurrenciesSettingsResult(){
        $value = isset($this->xmlData->UpdateInstanceCurrenciesSettingsResult) ? $this->xmlData->UpdateInstanceCurrenciesSettingsResult : false;
        return new VoidOtapiAnswer($value);
    }
}