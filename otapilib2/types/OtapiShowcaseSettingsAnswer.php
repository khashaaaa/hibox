<?php

class OtapiShowcaseSettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiShowcaseSettings
     */
    public function GetSettings(){
        $value = isset($this->xmlData->Settings) ? $this->xmlData->Settings : false;
        return new OtapiShowcaseSettings($value);
    }
}