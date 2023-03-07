<?php

class OtapiSetMarketsMerchPriceConfigXMLResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetSetMarketsMerchPriceConfigXMLResult(){
        $value = isset($this->xmlData->SetMarketsMerchPriceConfigXMLResult) ? $this->xmlData->SetMarketsMerchPriceConfigXMLResult : false;
        return new VoidOtapiAnswer($value);
    }
}