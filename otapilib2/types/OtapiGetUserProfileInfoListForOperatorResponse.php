<?php

class OtapiGetUserProfileInfoListForOperatorResponse extends BaseOtapiType{
    /**
     * @return OtapiUserProfileInfoListAnswer
     */
    public function GetGetUserProfileInfoListForOperatorResult(){
        $value = isset($this->xmlData->GetUserProfileInfoListForOperatorResult) ? $this->xmlData->GetUserProfileInfoListForOperatorResult : false;
        return new OtapiUserProfileInfoListAnswer($value);
    }
}