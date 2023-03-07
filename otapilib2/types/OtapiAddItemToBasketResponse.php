<?php

class OtapiAddItemToBasketResponse extends BaseOtapiType{
    /**
     * @return OtapiElementIdAnswer
     */
    public function GetAddItemToBasketResult(){
        $value = isset($this->xmlData->AddItemToBasketResult) ? $this->xmlData->AddItemToBasketResult : false;
        return new OtapiElementIdAnswer($value);
    }
}