<?php

class OtapiCreateUserProfileResponse extends BaseOtapiType{
    /**
     * @return OtapiUserProfileIdAnswer
     */
    public function GetCreateUserProfileResult(){
        $value = isset($this->xmlData->CreateUserProfileResult) ? $this->xmlData->CreateUserProfileResult : false;
        return new OtapiUserProfileIdAnswer($value);
    }
}