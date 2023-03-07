<?php

class OtapiDataListOfTranslationInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfTranslationInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfTranslationInfo($value);
    }
}