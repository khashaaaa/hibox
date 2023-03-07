<?php

class OtapiGetUserInfoForOperatorResponse extends BaseOtapiType{
    /**
     * @return OtapiUserInfoAnswer
     */
    public function GetGetUserInfoForOperatorResult(){
        $value = isset($this->xmlData->GetUserInfoForOperatorResult) ? $this->xmlData->GetUserInfoForOperatorResult : false;
        return new OtapiUserInfoAnswer($value);
    }
}