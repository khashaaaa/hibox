<?php

class OtapiGetOrderUserStatementForOperatorResponse extends BaseOtapiType{
    /**
     * @return OtapiAccountStatementAdministrationAnswer
     */
    public function GetGetOrderUserStatementForOperatorResult(){
        $value = isset($this->xmlData->GetOrderUserStatementForOperatorResult) ? $this->xmlData->GetOrderUserStatementForOperatorResult : false;
        return new OtapiAccountStatementAdministrationAnswer($value);
    }
}