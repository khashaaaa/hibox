<?php

class OtapiGetStatementResponse extends BaseOtapiType{
    /**
     * @return OtapiAccountStatementAnswer
     */
    public function GetGetStatementResult(){
        $value = isset($this->xmlData->GetStatementResult) ? $this->xmlData->GetStatementResult : false;
        return new OtapiAccountStatementAnswer($value);
    }
}