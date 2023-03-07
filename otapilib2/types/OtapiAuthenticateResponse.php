<?php

class OtapiAuthenticateResponse extends BaseOtapiType{
    /**
     * @return OtapiAuthenticateAnswer
     */
    public function GetAuthenticateResult(){
        $value = isset($this->xmlData->AuthenticateResult) ? $this->xmlData->AuthenticateResult : false;
        return new OtapiAuthenticateAnswer($value);
    }
}