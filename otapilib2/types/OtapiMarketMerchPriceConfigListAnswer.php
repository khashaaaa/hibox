<?php

class OtapiMarketMerchPriceConfigListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfMarketMerchPriceConfig
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfMarketMerchPriceConfig($value);
    }
}