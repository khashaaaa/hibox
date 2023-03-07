<?php

class OtapiSimplifiedItemInfo extends OtapiSimplifiedBaseItemInfo{
    /**
     * @return OtapiSimplifiedValueWithIdOfBoolean
     */
    public function GetAvailability(){
        $value = isset($this->xmlData->Availability) ? $this->xmlData->Availability : false;
        return new OtapiSimplifiedValueWithIdOfBoolean($value);
    }
    /**
     * @return OtapiSimplifiedItemPriceInfo
     */
    public function GetPrice(){
        $value = isset($this->xmlData->Price) ? $this->xmlData->Price : false;
        return new OtapiSimplifiedItemPriceInfo($value);
    }
    /**
     * @return OtapiArrayOfSimplifiedQuantityRange
     */
    public function GetQuantityRanges(){
        $value = isset($this->xmlData->QuantityRanges) ? $this->xmlData->QuantityRanges : false;
        return new OtapiArrayOfSimplifiedQuantityRange($value);
    }
}