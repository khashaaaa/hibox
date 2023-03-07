<?php

class OtapiGetItemMarketPriceListResponse extends BaseOtapiType{
    /**
     * @return OtapiItemMarketPriceInfoListAnswer
     */
    public function GetGetItemMarketPriceListResult(){
        $value = isset($this->xmlData->GetItemMarketPriceListResult) ? $this->xmlData->GetItemMarketPriceListResult : false;
        return new OtapiItemMarketPriceInfoListAnswer($value);
    }
}