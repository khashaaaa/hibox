<?php

class OtapiUpdateOrderResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateOrderResult(){
        $value = isset($this->xmlData->UpdateOrderResult) ? $this->xmlData->UpdateOrderResult : false;
        return new VoidOtapiAnswer($value);
    }
}