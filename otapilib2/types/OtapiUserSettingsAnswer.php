<?php

class OtapiUserSettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiUserSettings
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiUserSettings($value);
    }
}