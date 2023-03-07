<?php

class OtapiCollectionsSettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiCollectionsSettings
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiCollectionsSettings($value);
    }
}