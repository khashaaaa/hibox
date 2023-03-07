<?php

class OtapiYandexMarketIntegrationSettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiYandexMarketIntegrationSettings
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiYandexMarketIntegrationSettings($value);
    }
}