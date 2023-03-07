<?php

class OtapiClearBasketResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetClearBasketResult(){
        $value = isset($this->xmlData->ClearBasketResult) ? $this->xmlData->ClearBasketResult : false;
        return new VoidOtapiAnswer($value);
    }
}