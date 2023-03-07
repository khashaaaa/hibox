<?php

class OtapiCreateUserProfileForOperatorResponse extends BaseOtapiType{
    /**
     * @return OtapiUserProfileIdAnswer
     */
    public function GetCreateUserProfileForOperatorResult(){
        $value = isset($this->xmlData->CreateUserProfileForOperatorResult) ? $this->xmlData->CreateUserProfileForOperatorResult : false;
        return new OtapiUserProfileIdAnswer($value);
    }
}