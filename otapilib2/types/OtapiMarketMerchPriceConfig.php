<?php

class OtapiMarketMerchPriceConfig extends BaseOtapiType{
    /**
     * @return OtapiMerchId
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? $this->xmlData->Id : false;
        return new OtapiMerchId($value);
    }
    /**
     * @return OtapiArrayOfPriceFormationGroupInfo
     */
    public function GetPriceFormationGroupInfoList(){
        $value = isset($this->xmlData->PriceFormationGroupInfoList) ? $this->xmlData->PriceFormationGroupInfoList : false;
        return new OtapiArrayOfPriceFormationGroupInfo($value);
    }
    /**
     * @return OtapiShowcaseSettings
     */
    public function GetShowcaseSettings(){
        $value = isset($this->xmlData->ShowcaseSettings) ? $this->xmlData->ShowcaseSettings : false;
        return new OtapiShowcaseSettings($value);
    }
}