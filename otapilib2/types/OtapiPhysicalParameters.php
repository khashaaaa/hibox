<?php

class OtapiPhysicalParameters extends BaseOtapiType{
    /**
     * @return decimal
     */
    public function GetApproxWeight(){
        $value = isset($this->xmlData->ApproxWeight) ? (string)$this->xmlData->ApproxWeight : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetWeight(){
        $value = isset($this->xmlData->Weight) ? (string)$this->xmlData->Weight : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetSize(){
        $value = isset($this->xmlData->Size) ? (string)$this->xmlData->Size : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}