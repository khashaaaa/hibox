<?php

class OtapiDataListOfLanguageInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfLanguageInfo1
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfLanguageInfo1($value);
    }
}