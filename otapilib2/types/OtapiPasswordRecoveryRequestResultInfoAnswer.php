<?php

class OtapiPasswordRecoveryRequestResultInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiPasswordRecoveryRequestResultInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiPasswordRecoveryRequestResultInfo($value);
    }
}