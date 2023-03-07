<?php

class OtapiQuantityRange extends OtapiAbstractCustomizablePrice{
    /**
     * @return long
     */
    public function GetMinQuantity(){
        $value = isset($this->xmlData->MinQuantity) ? (string)$this->xmlData->MinQuantity : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiPrice
     */
    public function GetPrice(){
        $value = isset($this->xmlData->Price) ? $this->xmlData->Price : false;
        return new OtapiPrice($value);
    }
}