<?php

class OtapiSocialNetworkSettings extends BaseOtapiType{
    /**
     * @return OtapiMetaListOfSocialNetworkOptionsData
     */
    public function GetOptionsList(){
        $value = isset($this->xmlData->OptionsList) ? $this->xmlData->OptionsList : false;
        return new OtapiMetaListOfSocialNetworkOptionsData($value);
    }
    /**
     * @return OtapiMetaInfo
     */
    public function GetMetaInfo(){
        $value = isset($this->xmlData->MetaInfo) ? $this->xmlData->MetaInfo : false;
        return new OtapiMetaInfo($value);
    }
}