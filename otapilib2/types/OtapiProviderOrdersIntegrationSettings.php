<?php

class OtapiProviderOrdersIntegrationSettings extends BaseOtapiType{
    /**
     * @return OtapiMetaInfo
     */
    public function GetMetaInfo(){
        $value = isset($this->xmlData->MetaInfo) ? $this->xmlData->MetaInfo : false;
        return new OtapiMetaInfo($value);
    }
    /**
     * @return OtapiTaobaoOrdersIntegrationSettings
     */
    public function GetTaobao(){
        $value = isset($this->xmlData->Taobao) ? $this->xmlData->Taobao : false;
        return new OtapiTaobaoOrdersIntegrationSettings($value);
    }
}