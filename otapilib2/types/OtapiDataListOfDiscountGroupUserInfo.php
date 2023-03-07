<?php

class OtapiDataListOfDiscountGroupUserInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfDiscountGroupUserInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfDiscountGroupUserInfo($value);
    }
}