<?php

class OtapiAdditionalPriceInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetDisplayName(){
        $value = isset($this->xmlData->DisplayName) ? (string)$this->xmlData->DisplayName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetType(){
        $value = isset($this->xmlData->Type) ? (string)$this->xmlData->Type : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetName(){
        $value = isset($this->xmlData->Name) ? (string)$this->xmlData->Name : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiBasePrice
     */
    public function GetPrice(){
        $value = isset($this->xmlData->Price) ? $this->xmlData->Price : false;
        return new OtapiBasePrice($value);
    }
    /**
     * @return OtapiArrayOfInt
     */
    public function GetElementIds(){
        $value = isset($this->xmlData->ElementIds) ? $this->xmlData->ElementIds : false;
        return new OtapiArrayOfInt($value);
    }
}