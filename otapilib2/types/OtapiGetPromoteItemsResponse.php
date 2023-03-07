<?php

class OtapiGetPromoteItemsResponse extends BaseOtapiType{
    /**
     * @return OtapiPromoItemInfoListAnswer
     */
    public function GetGetPromoteItemsResult(){
        $value = isset($this->xmlData->GetPromoteItemsResult) ? $this->xmlData->GetPromoteItemsResult : false;
        return new OtapiPromoItemInfoListAnswer($value);
    }
}