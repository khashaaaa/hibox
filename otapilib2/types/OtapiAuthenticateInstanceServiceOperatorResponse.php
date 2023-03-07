<?php

class OtapiAuthenticateInstanceServiceOperatorResponse extends BaseOtapiType{
    /**
     * @return OtapiSessionIdAnswer
     */
    public function GetAuthenticateInstanceServiceOperatorResult(){
        $value = isset($this->xmlData->AuthenticateInstanceServiceOperatorResult) ? $this->xmlData->AuthenticateInstanceServiceOperatorResult : false;
        return new OtapiSessionIdAnswer($value);
    }
}