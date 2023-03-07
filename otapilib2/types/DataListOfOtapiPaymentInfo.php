<?php

class DataListOfOtapiPaymentInfo extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiPaymentInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiPaymentInfo($value);
    }
}