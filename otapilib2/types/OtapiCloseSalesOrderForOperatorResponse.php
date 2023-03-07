<?php

class OtapiCloseSalesOrderForOperatorResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetCloseSalesOrderForOperatorResult(){
        $value = isset($this->xmlData->CloseSalesOrderForOperatorResult) ? $this->xmlData->CloseSalesOrderForOperatorResult : false;
        return new VoidOtapiAnswer($value);
    }
}