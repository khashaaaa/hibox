<?php

class OtapiMarketMerchInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfMarketMerchInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfMarketMerchInfo($value);
    }
}