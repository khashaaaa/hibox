<?php

class OtapiAuthenticateInstanceOperatorResponse extends BaseOtapiType{
    /**
     * @return OtapiSessionIdAnswer
     */
    public function GetAuthenticateInstanceOperatorResult(){
        $value = isset($this->xmlData->AuthenticateInstanceOperatorResult) ? $this->xmlData->AuthenticateInstanceOperatorResult : false;
        return new OtapiSessionIdAnswer($value);
    }
}