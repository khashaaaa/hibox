<?php

class OtapiSimplifiedItemPriceInfo extends BaseOtapiType{
    /**
     * @return OtapiSimplifiedPrice
     */
    public function GetOldPrice(){
        $value = isset($this->xmlData->OldPrice) ? $this->xmlData->OldPrice : false;
        return new OtapiSimplifiedPrice($value);
    }
    /**
     * @return OtapiSimplifiedPrice
     */
    public function GetPrice(){
        $value = isset($this->xmlData->Price) ? $this->xmlData->Price : false;
        return new OtapiSimplifiedPrice($value);
    }
    /**
     * @return int
     */
    public function GetDiscountPercent(){
        $value = isset($this->xmlData->DiscountPercent) ? (string)$this->xmlData->DiscountPercent : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiSimplifiedPrice
     */
    public function GetInternalDelivery(){
        $value = isset($this->xmlData->InternalDelivery) ? $this->xmlData->InternalDelivery : false;
        return new OtapiSimplifiedPrice($value);
    }
}