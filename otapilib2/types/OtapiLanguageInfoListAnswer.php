<?php

class OtapiLanguageInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfLanguageInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfLanguageInfo($value);
    }
}