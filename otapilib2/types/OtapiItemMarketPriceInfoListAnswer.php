<?php

class OtapiItemMarketPriceInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfItemMarketPriceInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfItemMarketPriceInfo($value);
    }
}