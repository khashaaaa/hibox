<?php

class OtapiBackgroundActivityInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfBackgroundActivityInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfBackgroundActivityInfo($value);
    }
}