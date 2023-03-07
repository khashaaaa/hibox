<?php

class OtapiGetStatementForOperatorResponse extends BaseOtapiType{
    /**
     * @return OtapiAccountStatementAdministrationAnswer
     */
    public function GetGetStatementForOperatorResult(){
        $value = isset($this->xmlData->GetStatementForOperatorResult) ? $this->xmlData->GetStatementForOperatorResult : false;
        return new OtapiAccountStatementAdministrationAnswer($value);
    }
}