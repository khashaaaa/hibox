<?php

class OtapiRegisterUserResponse extends BaseOtapiType{
    /**
     * @return OtapiEmailConfirmationInfoAnswer
     */
    public function GetRegisterUserResult(){
        $value = isset($this->xmlData->RegisterUserResult) ? $this->xmlData->RegisterUserResult : false;
        return new OtapiEmailConfirmationInfoAnswer($value);
    }
}