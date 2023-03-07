<?php

class OtapiGetOrderUserAccountInfoForOperatorResponse extends BaseOtapiType{
    /**
     * @return OtapiAccountAdministrationInfoAnswer
     */
    public function GetGetOrderUserAccountInfoForOperatorResult(){
        $value = isset($this->xmlData->GetOrderUserAccountInfoForOperatorResult) ? $this->xmlData->GetOrderUserAccountInfoForOperatorResult : false;
        return new OtapiAccountAdministrationInfoAnswer($value);
    }
}