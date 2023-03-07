<?php

class OtapiGetBasketResponse extends BaseOtapiType{
    /**
     * @return OtapiCollectionInfoAnswer
     */
    public function GetGetBasketResult(){
        $value = isset($this->xmlData->GetBasketResult) ? $this->xmlData->GetBasketResult : false;
        return new OtapiCollectionInfoAnswer($value);
    }
}