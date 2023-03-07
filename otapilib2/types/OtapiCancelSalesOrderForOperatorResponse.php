<?php

class OtapiCancelSalesOrderForOperatorResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetCancelSalesOrderForOperatorResult(){
        $value = isset($this->xmlData->CancelSalesOrderForOperatorResult) ? $this->xmlData->CancelSalesOrderForOperatorResult : false;
        return new VoidOtapiAnswer($value);
    }
}