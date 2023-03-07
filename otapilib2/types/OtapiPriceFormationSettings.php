<?php

class OtapiPriceFormationSettings extends BaseOtapiType{
    /**
     * @return OtapiCurrencyValue
     */
    public function GetInternalDeliveryPrice(){
        $value = isset($this->xmlData->InternalDeliveryPrice) ? $this->xmlData->InternalDeliveryPrice : false;
        return new OtapiCurrencyValue($value);
    }
    /**
     * @return OtapiArrayOfPriceFormationIntervalInfo
     */
    public function GetPriceFormationIntervals(){
        $value = isset($this->xmlData->PriceFormationIntervals) ? $this->xmlData->PriceFormationIntervals : false;
        return new OtapiArrayOfPriceFormationIntervalInfo($value);
    }
    /**
     * @return int
     */
    public function GetPriceRoundingFactor(){
        $value = isset($this->xmlData->PriceRoundingFactor) ? (string)$this->xmlData->PriceRoundingFactor : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function RoundOriginalInternalDeliveryPrice(){
        $value = isset($this->xmlData->RoundOriginalInternalDeliveryPrice) ? (string)$this->xmlData->RoundOriginalInternalDeliveryPrice : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}