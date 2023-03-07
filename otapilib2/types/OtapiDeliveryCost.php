<?php

class OtapiDeliveryCost extends OtapiAbstractCustomizablePrice{
    /**
     * @return string
     */
    public function GetAreaCode(){
        $value = isset($this->xmlData->AreaCode) ? (string)$this->xmlData->AreaCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetMode(){
        $value = isset($this->xmlData->Mode) ? (string)$this->xmlData->Mode : false;
        $propertyType = 'string';
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
     * @return decimal
     */
    public function GetStartCost(){
        $value = isset($this->xmlData->StartCost) ? (string)$this->xmlData->StartCost : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetStartWeight(){
        $value = isset($this->xmlData->StartWeight) ? (string)$this->xmlData->StartWeight : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetAddWeight(){
        $value = isset($this->xmlData->AddWeight) ? (string)$this->xmlData->AddWeight : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetAddCost(){
        $value = isset($this->xmlData->AddCost) ? (string)$this->xmlData->AddCost : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}