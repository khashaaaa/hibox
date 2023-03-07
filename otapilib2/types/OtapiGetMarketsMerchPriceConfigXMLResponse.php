<?php

class OtapiGetMarketsMerchPriceConfigXMLResponse extends BaseOtapiType{
    /**
     * @return OtapiMarketMerchPriceConfigListAnswer
     */
    public function GetGetMarketsMerchPriceConfigXMLResult(){
        $value = isset($this->xmlData->GetMarketsMerchPriceConfigXMLResult) ? $this->xmlData->GetMarketsMerchPriceConfigXMLResult : false;
        return new OtapiMarketMerchPriceConfigListAnswer($value);
    }
}