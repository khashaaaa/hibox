<?php

class OtapiMessengerSettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiMessengerSettings
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiMessengerSettings($value);
    }
}