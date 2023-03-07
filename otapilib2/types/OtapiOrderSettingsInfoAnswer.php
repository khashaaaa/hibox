<?php

class OtapiOrderSettingsInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiOrderSettingsInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiOrderSettingsInfo($value);
    }
}