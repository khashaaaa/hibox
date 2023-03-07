<?php

class OtapiPriceFormationIntervalInfo extends BaseOtapiType{
    /**
     * @return OtapiMargin
     */
    public function GetMarginPercent(){
        $value = isset($this->xmlData->MarginPercent) ? $this->xmlData->MarginPercent : false;
        return new OtapiMargin($value);
    }
    /**
     * @return OtapiCurrencyValue
     */
    public function GetMarginFixed(){
        $value = isset($this->xmlData->MarginFixed) ? $this->xmlData->MarginFixed : false;
        return new OtapiCurrencyValue($value);
    }
    /**
     * @return OtapiCurrencyValue
     */
    public function GetMinimumLimit(){
        $value = isset($this->xmlData->MinimumLimit) ? $this->xmlData->MinimumLimit : false;
        return new OtapiCurrencyValue($value);
    }
    /**
     * @return OtapiCurrencyValue
     */
    public function GetInternalDeliveryPrice(){
        $value = isset($this->xmlData->InternalDeliveryPrice) ? $this->xmlData->InternalDeliveryPrice : false;
        return new OtapiCurrencyValue($value);
    }
    /**
    * @return int
    */
    public function GetIdAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Id']) ? (string)$attributes['Id'] : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}