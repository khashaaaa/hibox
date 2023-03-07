<?php

class OtapiProviderSettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiProviderSettings
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiProviderSettings($value);
    }
}