<?php

class OtapiGetUserDiscountGroupForOperatorResponse extends BaseOtapiType{
    /**
     * @return OtapiDiscountGroupInfoAnswer
     */
    public function GetGetUserDiscountGroupForOperatorResult(){
        $value = isset($this->xmlData->GetUserDiscountGroupForOperatorResult) ? $this->xmlData->GetUserDiscountGroupForOperatorResult : false;
        return new OtapiDiscountGroupInfoAnswer($value);
    }
}