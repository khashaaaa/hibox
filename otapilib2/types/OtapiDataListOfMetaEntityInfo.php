<?php

class OtapiDataListOfMetaEntityInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfMetaEntityInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfMetaEntityInfo($value);
    }
}