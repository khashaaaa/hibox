<?php

class OtapiGetUserInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiUserInfoAnswer
     */
    public function GetGetUserInfoResult(){
        $value = isset($this->xmlData->GetUserInfoResult) ? $this->xmlData->GetUserInfoResult : false;
        return new OtapiUserInfoAnswer($value);
    }
}