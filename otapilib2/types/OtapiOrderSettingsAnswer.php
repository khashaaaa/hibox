<?php

class OtapiOrderSettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiOrderSettings
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiOrderSettings($value);
    }
}