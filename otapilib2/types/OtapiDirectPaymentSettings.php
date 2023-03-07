<?php

class OtapiDirectPaymentSettings extends BaseOtapiType{
    /**
     * @return OtapiMetaListOfDirectPaymentModeSettings
     */
    public function GetModeSettingsList(){
        $value = isset($this->xmlData->ModeSettingsList) ? $this->xmlData->ModeSettingsList : false;
        return new OtapiMetaListOfDirectPaymentModeSettings($value);
    }
    /**
     * @return OtapiValueListOfString
     */
    public function GetActiveModes(){
        $value = isset($this->xmlData->ActiveModes) ? $this->xmlData->ActiveModes : false;
        return new OtapiValueListOfString($value);
    }
    /**
     * @return OtapiMetaInfo
     */
    public function GetMetaInfo(){
        $value = isset($this->xmlData->MetaInfo) ? $this->xmlData->MetaInfo : false;
        return new OtapiMetaInfo($value);
    }
}