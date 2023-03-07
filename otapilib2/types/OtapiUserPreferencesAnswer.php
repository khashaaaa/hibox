<?php

class OtapiUserPreferencesAnswer extends OtapiAnswer{
    /**
     * @return OtapiUserPreferences
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiUserPreferences($value);
    }
}