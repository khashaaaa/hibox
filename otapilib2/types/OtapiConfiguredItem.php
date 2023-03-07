<?php

class OtapiConfiguredItem extends OtapiAbstractCustomizablePrice{
    /**
     * @return string
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? (string)$this->xmlData->Id : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return long
     */
    public function GetQuantity(){
        $value = isset($this->xmlData->Quantity) ? (string)$this->xmlData->Quantity : false;
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
    /**
     * @return ArrayOfOtapiValuedConfigurator
     */
    public function GetConfigurators(){
        $value = isset($this->xmlData->Configurators) ? $this->xmlData->Configurators : false;
        return new ArrayOfOtapiValuedConfigurator($value);
    }
}