<?php

class OtapiGetUserStatusInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiUserStatusInfoAnswer
     */
    public function GetGetUserStatusInfoResult(){
        $value = isset($this->xmlData->GetUserStatusInfoResult) ? $this->xmlData->GetUserStatusInfoResult : false;
        return new OtapiUserStatusInfoAnswer($value);
    }
}