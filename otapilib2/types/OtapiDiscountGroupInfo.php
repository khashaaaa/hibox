<?php

class OtapiDiscountGroupInfo extends BaseOtapiType{
    /**
     * @return OtapiDiscountGroupId
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? $this->xmlData->Id : false;
        return new OtapiDiscountGroupId($value);
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
     * @return OtapiDiscountInfo
     */
    public function GetDiscount(){
        $value = isset($this->xmlData->Discount) ? $this->xmlData->Discount : false;
        return new OtapiDiscountInfo($value);
    }
    /**
     * @return OtapiDiscountIdentificationParametrInfo
     */
    public function GetDiscountIdentificationParametr(){
        $value = isset($this->xmlData->DiscountIdentificationParametr) ? $this->xmlData->DiscountIdentificationParametr : false;
        return new OtapiDiscountIdentificationParametrInfo($value);
    }
}