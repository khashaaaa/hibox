<?php

class OtapiEditBasketItemQuantityResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetEditBasketItemQuantityResult(){
        $value = isset($this->xmlData->EditBasketItemQuantityResult) ? $this->xmlData->EditBasketItemQuantityResult : false;
        return new VoidOtapiAnswer($value);
    }
}