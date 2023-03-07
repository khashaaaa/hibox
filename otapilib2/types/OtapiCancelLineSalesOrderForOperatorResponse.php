<?php

class OtapiCancelLineSalesOrderForOperatorResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetCancelLineSalesOrderForOperatorResult(){
        $value = isset($this->xmlData->CancelLineSalesOrderForOperatorResult) ? $this->xmlData->CancelLineSalesOrderForOperatorResult : false;
        return new VoidOtapiAnswer($value);
    }
}