<?php

class OtapiDataListOfDiscountGroupInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfDiscountGroupInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfDiscountGroupInfo($value);
    }
}