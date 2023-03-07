<?php

class OtapiItemInfo extends OtapiBaseItemInfo{
    /**
     * @return OtapiBasePrice
     */
    public function GetPromotionPrice(){
        $value = isset($this->xmlData->PromotionPrice) ? $this->xmlData->PromotionPrice : false;
        return new OtapiBasePrice($value);
    }
    /**
     * @return OtapiArrayOfPricePercent
     */
    public function GetPromotionPricePercent(){
        $value = isset($this->xmlData->PromotionPricePercent) ? $this->xmlData->PromotionPricePercent : false;
        return new OtapiArrayOfPricePercent($value);
    }
}