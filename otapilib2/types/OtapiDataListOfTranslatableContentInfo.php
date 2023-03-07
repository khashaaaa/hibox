<?php

class OtapiDataListOfTranslatableContentInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfTranslatableContentInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfTranslatableContentInfo($value);
    }
}