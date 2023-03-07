<?php

class OtapiCloseOrderResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetCloseOrderResult(){
        $value = isset($this->xmlData->CloseOrderResult) ? $this->xmlData->CloseOrderResult : false;
        return new VoidOtapiAnswer($value);
    }
}