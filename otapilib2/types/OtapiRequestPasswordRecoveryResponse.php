<?php

class OtapiRequestPasswordRecoveryResponse extends BaseOtapiType{
    /**
     * @return OtapiPasswordRecoveryRequestResultInfoAnswer
     */
    public function GetRequestPasswordRecoveryResult(){
        $value = isset($this->xmlData->RequestPasswordRecoveryResult) ? $this->xmlData->RequestPasswordRecoveryResult : false;
        return new OtapiPasswordRecoveryRequestResultInfoAnswer($value);
    }
}