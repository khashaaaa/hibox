<?php

class OtapiEditBasketItemFieldsResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetEditBasketItemFieldsResult(){
        $value = isset($this->xmlData->EditBasketItemFieldsResult) ? $this->xmlData->EditBasketItemFieldsResult : false;
        return new VoidOtapiAnswer($value);
    }
}