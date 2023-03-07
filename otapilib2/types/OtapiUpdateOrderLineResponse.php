<?php

class OtapiUpdateOrderLineResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateOrderLineResult(){
        $value = isset($this->xmlData->UpdateOrderLineResult) ? $this->xmlData->UpdateOrderLineResult : false;
        return new VoidOtapiAnswer($value);
    }
}