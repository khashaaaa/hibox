<?php

class OtapiRemoveItemFromBasketResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetRemoveItemFromBasketResult(){
        $value = isset($this->xmlData->RemoveItemFromBasketResult) ? $this->xmlData->RemoveItemFromBasketResult : false;
        return new VoidOtapiAnswer($value);
    }
}