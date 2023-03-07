<?php

class OtapiDataListOfItemMarketPriceInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfItemMarketPriceInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfItemMarketPriceInfo($value);
    }
}