<?php

class OtapiDeliveryMode extends OtapiAbstractCustomizablePrice{
    /**
     * @return string
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? (string)$this->xmlData->Id : false;
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
     * @return string
     */
    public function GetDescription(){
        $value = isset($this->xmlData->Description) ? (string)$this->xmlData->Description : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetPrice(){
        $value = isset($this->xmlData->Price) ? (string)$this->xmlData->Price : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiBasePrice
     */
    public function GetFullPrice(){
        $value = isset($this->xmlData->FullPrice) ? $this->xmlData->FullPrice : false;
        return new OtapiBasePrice($value);
    }
    /**
     * @return string
     */
    public function GetCurrencyCode(){
        $value = isset($this->xmlData->CurrencyCode) ? (string)$this->xmlData->CurrencyCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCurrencySign(){
        $value = isset($this->xmlData->CurrencySign) ? (string)$this->xmlData->CurrencySign : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetErrorCode(){
        $value = isset($this->xmlData->ErrorCode) ? (string)$this->xmlData->ErrorCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetErrorDescription(){
        $value = isset($this->xmlData->ErrorDescription) ? (string)$this->xmlData->ErrorDescription : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}