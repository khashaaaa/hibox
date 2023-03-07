<?php

class OtapiPurchaseItemsResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetPurchaseItemsResult(){
        $value = isset($this->xmlData->PurchaseItemsResult) ? $this->xmlData->PurchaseItemsResult : false;
        return new VoidOtapiAnswer($value);
    }
}