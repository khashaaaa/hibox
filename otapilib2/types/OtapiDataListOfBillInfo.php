<?php

class OtapiDataListOfBillInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfBillInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfBillInfo($value);
    }
}