<?php

class OtapiUserProfileSettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiUserProfileSettings
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiUserProfileSettings($value);
    }
}