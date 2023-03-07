<?php

class OtapiGetAccountInfoForOperatorResponse extends BaseOtapiType{
    /**
     * @return OtapiAccountAdministrationInfoAnswer
     */
    public function GetGetAccountInfoForOperatorResult(){
        $value = isset($this->xmlData->GetAccountInfoForOperatorResult) ? $this->xmlData->GetAccountInfoForOperatorResult : false;
        return new OtapiAccountAdministrationInfoAnswer($value);
    }
}