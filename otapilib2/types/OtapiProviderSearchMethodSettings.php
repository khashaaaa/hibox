<?php

class OtapiProviderSearchMethodSettings extends BaseOtapiType{
    /**
     * @return OtapiMetaInfo
     */
    public function GetMetaInfo(){
        $value = isset($this->xmlData->MetaInfo) ? $this->xmlData->MetaInfo : false;
        return new OtapiMetaInfo($value);
    }
    /**
     * @return OtapiValueListOfString
     */
    public function GetSearchMethods(){
        $value = isset($this->xmlData->SearchMethods) ? $this->xmlData->SearchMethods : false;
        return new OtapiValueListOfString($value);
    }
}