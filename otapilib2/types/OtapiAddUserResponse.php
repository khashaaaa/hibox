<?php

class OtapiAddUserResponse extends BaseOtapiType{
    /**
     * @return OtapiUserIdAnswer
     */
    public function GetAddUserResult(){
        $value = isset($this->xmlData->AddUserResult) ? $this->xmlData->AddUserResult : false;
        return new OtapiUserIdAnswer($value);
    }
}