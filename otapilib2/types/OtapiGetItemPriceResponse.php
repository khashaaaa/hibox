<?php

class OtapiGetItemPriceResponse extends BaseOtapiType{
    /**
     * @return OtapiPriceAnswer
     */
    public function GetGetItemPriceResult(){
        $value = isset($this->xmlData->GetItemPriceResult) ? $this->xmlData->GetItemPriceResult : false;
        return new OtapiPriceAnswer($value);
    }
}