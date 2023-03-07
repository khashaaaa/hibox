<?php

class OtapiEditablePriceFormationSettings extends BaseOtapiType{
    /**
    * @return int
    */
    public function GetPriceRoundingFactorAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['PriceRoundingFactor']) ? (string)$attributes['PriceRoundingFactor'] : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return boolean
    */
    public function RoundOriginalInternalDeliveryPriceAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['RoundOriginalInternalDeliveryPrice']) ? (string)$attributes['RoundOriginalInternalDeliveryPrice'] : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}