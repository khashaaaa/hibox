<?php

class OtapiProviderOrdersIntegrationSettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiProviderOrdersIntegrationSettings
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiProviderOrdersIntegrationSettings($value);
    }
}