<?php

class OtapiLogEntryInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataSubListOfLogEntryInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataSubListOfLogEntryInfo($value);
    }
}