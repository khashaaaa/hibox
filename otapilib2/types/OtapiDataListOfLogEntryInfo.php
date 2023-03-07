<?php

class OtapiDataListOfLogEntryInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfLogEntryInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfLogEntryInfo($value);
    }
}