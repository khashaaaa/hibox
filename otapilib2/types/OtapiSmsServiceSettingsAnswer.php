<?php

class OtapiSmsServiceSettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiSmsServiceSettings
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiSmsServiceSettings($value);
    }
}