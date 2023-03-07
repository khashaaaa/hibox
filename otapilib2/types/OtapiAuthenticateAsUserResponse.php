<?php

class OtapiAuthenticateAsUserResponse extends BaseOtapiType{
    /**
     * @return OtapiAuthenticateAsUserAnswer
     */
    public function GetAuthenticateAsUserResult(){
        $value = isset($this->xmlData->AuthenticateAsUserResult) ? $this->xmlData->AuthenticateAsUserResult : false;
        return new OtapiAuthenticateAsUserAnswer($value);
    }
}