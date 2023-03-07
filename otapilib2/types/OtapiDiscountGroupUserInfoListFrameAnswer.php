<?php

class OtapiDiscountGroupUserInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataSubListOfDiscountGroupUserInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataSubListOfDiscountGroupUserInfo($value);
    }
}