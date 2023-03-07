<?php

class OtapiCreateOrderResponse extends BaseOtapiType{
    /**
     * @return OtapiOrderInfoAnswer
     */
    public function GetCreateOrderResult(){
        $value = isset($this->xmlData->CreateOrderResult) ? $this->xmlData->CreateOrderResult : false;
        return new OtapiOrderInfoAnswer($value);
    }
}