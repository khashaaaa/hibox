<?php

class OtapiDataListOfMarketMerchInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfMarketMerchInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfMarketMerchInfo($value);
    }
}