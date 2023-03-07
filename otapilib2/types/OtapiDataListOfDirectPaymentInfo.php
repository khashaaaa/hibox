<?php

class OtapiDataListOfDirectPaymentInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfDirectPaymentInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfDirectPaymentInfo($value);
    }
}