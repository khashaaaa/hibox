<?php

class OtapiTariffInfo extends BaseOtapiType{
    /**
     * @return int
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? (string)$this->xmlData->Id : false;
        $propertyType = 'int';
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
     * @return boolean
     */
    public function IsEnabled(){
        $value = isset($this->xmlData->IsEnabled) ? (string)$this->xmlData->IsEnabled : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetCallLimit(){
        $value = isset($this->xmlData->CallLimit) ? (string)$this->xmlData->CallLimit : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetCallPrice(){
        $value = isset($this->xmlData->CallPrice) ? (string)$this->xmlData->CallPrice : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetTurnoverPercent(){
        $value = isset($this->xmlData->TurnoverPercent) ? (string)$this->xmlData->TurnoverPercent : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetMinimumRent(){
        $value = isset($this->xmlData->MinimumRent) ? (string)$this->xmlData->MinimumRent : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetFixedPrice(){
        $value = isset($this->xmlData->FixedPrice) ? (string)$this->xmlData->FixedPrice : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetMaximumRent(){
        $value = isset($this->xmlData->MaximumRent) ? (string)$this->xmlData->MaximumRent : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}