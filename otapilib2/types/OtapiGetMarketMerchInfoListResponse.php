<?php

class OtapiGetMarketMerchInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiMarketMerchInfoListAnswer
     */
    public function GetGetMarketMerchInfoListResult(){
        $value = isset($this->xmlData->GetMarketMerchInfoListResult) ? $this->xmlData->GetMarketMerchInfoListResult : false;
        return new OtapiMarketMerchInfoListAnswer($value);
    }
}