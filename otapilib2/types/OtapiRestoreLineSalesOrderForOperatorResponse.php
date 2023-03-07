<?php

class OtapiRestoreLineSalesOrderForOperatorResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetRestoreLineSalesOrderForOperatorResult(){
        $value = isset($this->xmlData->RestoreLineSalesOrderForOperatorResult) ? $this->xmlData->RestoreLineSalesOrderForOperatorResult : false;
        return new VoidOtapiAnswer($value);
    }
}