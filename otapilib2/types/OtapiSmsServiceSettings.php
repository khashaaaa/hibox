<?php

class OtapiSmsServiceSettings extends BaseOtapiType{
    /**
     * @return OtapiMetaListOfSmsServiceModeSettings
     */
    public function GetSmsServiceModeSettingsList(){
        $value = isset($this->xmlData->SmsServiceModeSettingsList) ? $this->xmlData->SmsServiceModeSettingsList : false;
        return new OtapiMetaListOfSmsServiceModeSettings($value);
    }
    /**
     * @return boolean
     */
    public function IsSmsEnabled(){
        $value = isset($this->xmlData->IsSmsEnabled) ? (string)$this->xmlData->IsSmsEnabled : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiMetaInfo
     */
    public function GetMetaInfo(){
        $value = isset($this->xmlData->MetaInfo) ? $this->xmlData->MetaInfo : false;
        return new OtapiMetaInfo($value);
    }
}