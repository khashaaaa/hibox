<?php

class OtapiDataListOfOperatorInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfOperatorInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfOperatorInfo($value);
    }
}