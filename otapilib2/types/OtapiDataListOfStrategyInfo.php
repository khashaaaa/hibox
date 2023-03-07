<?php

class OtapiDataListOfStrategyInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfStrategyInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfStrategyInfo($value);
    }
}