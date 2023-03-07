<?php

class OtapiUpdateOrderLineForOperatorResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateOrderLineForOperatorResult(){
        $value = isset($this->xmlData->UpdateOrderLineForOperatorResult) ? $this->xmlData->UpdateOrderLineForOperatorResult : false;
        return new VoidOtapiAnswer($value);
    }
}