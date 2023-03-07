<?php

class OtapiDataListOfMarketMerchPriceConfig extends BaseOtapiType{
    /**
     * @return OtapiArrayOfMarketMerchPriceConfig
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfMarketMerchPriceConfig($value);
    }
}