<?php

class OtapiWebUISettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiWebUISettings
     */
    public function GetSettings(){
        $value = isset($this->xmlData->Settings) ? $this->xmlData->Settings : false;
        return new OtapiWebUISettings($value);
    }
}