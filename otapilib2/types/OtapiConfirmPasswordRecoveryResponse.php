<?php

class OtapiConfirmPasswordRecoveryResponse extends BaseOtapiType{
    /**
     * @return OtapiPasswordRecoveryConfirmationResultInfoAnswer
     */
    public function GetConfirmPasswordRecoveryResult(){
        $value = isset($this->xmlData->ConfirmPasswordRecoveryResult) ? $this->xmlData->ConfirmPasswordRecoveryResult : false;
        return new OtapiPasswordRecoveryConfirmationResultInfoAnswer($value);
    }
}