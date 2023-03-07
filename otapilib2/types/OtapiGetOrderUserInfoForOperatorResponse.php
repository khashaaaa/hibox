<?php

class OtapiGetOrderUserInfoForOperatorResponse extends BaseOtapiType{
    /**
     * @return OtapiUserInfoAnswer
     */
    public function GetGetOrderUserInfoForOperatorResult(){
        $value = isset($this->xmlData->GetOrderUserInfoForOperatorResult) ? $this->xmlData->GetOrderUserInfoForOperatorResult : false;
        return new OtapiUserInfoAnswer($value);
    }
}