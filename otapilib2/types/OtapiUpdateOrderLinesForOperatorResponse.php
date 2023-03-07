<?php

class OtapiUpdateOrderLinesForOperatorResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateOrderLinesForOperatorResult(){
        $value = isset($this->xmlData->UpdateOrderLinesForOperatorResult) ? $this->xmlData->UpdateOrderLinesForOperatorResult : false;
        return new VoidOtapiAnswer($value);
    }
}