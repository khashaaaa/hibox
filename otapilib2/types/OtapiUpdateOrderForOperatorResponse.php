<?php

class OtapiUpdateOrderForOperatorResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateOrderForOperatorResult(){
        $value = isset($this->xmlData->UpdateOrderForOperatorResult) ? $this->xmlData->UpdateOrderForOperatorResult : false;
        return new VoidOtapiAnswer($value);
    }
}