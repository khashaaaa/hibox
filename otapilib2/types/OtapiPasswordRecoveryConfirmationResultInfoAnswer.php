<?php

class OtapiPasswordRecoveryConfirmationResultInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiPasswordRecoveryConfirmationResultInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiPasswordRecoveryConfirmationResultInfo($value);
    }
}