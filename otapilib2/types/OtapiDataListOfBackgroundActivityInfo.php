<?php

class OtapiDataListOfBackgroundActivityInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfBackgroundActivityInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfBackgroundActivityInfo($value);
    }
}