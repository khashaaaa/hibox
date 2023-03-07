<?php

class OtapiProviderSearchMethodSettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiProviderSearchMethodSettings
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiProviderSearchMethodSettings($value);
    }
}