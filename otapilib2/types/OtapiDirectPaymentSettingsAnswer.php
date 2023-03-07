<?php

class OtapiDirectPaymentSettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiDirectPaymentSettings
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDirectPaymentSettings($value);
    }
}