<?php

class OtapiCurrencySettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiCurrencySettings
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiCurrencySettings($value);
    }
}