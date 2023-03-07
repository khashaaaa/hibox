<?php

class OtapiSelectorExportersSettings extends BaseOtapiType{
    /**
     * @return OtapiVkontakteIntegrationSettings
     */
    public function GetVkontakte(){
        $value = isset($this->xmlData->Vkontakte) ? $this->xmlData->Vkontakte : false;
        return new OtapiVkontakteIntegrationSettings($value);
    }
    /**
     * @return OtapiMetaInfo
     */
    public function GetMetaInfo(){
        $value = isset($this->xmlData->MetaInfo) ? $this->xmlData->MetaInfo : false;
        return new OtapiMetaInfo($value);
    }
}