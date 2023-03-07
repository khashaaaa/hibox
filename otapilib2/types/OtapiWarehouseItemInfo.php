<?php

class OtapiWarehouseItemInfo extends BaseOtapiType{
    /**
     * @return long
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? (string)$this->xmlData->Id : false;
        $propertyType = 'long';
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
     * @return string
     */
    public function GetMainImageUrl(){
        $value = isset($this->xmlData->MainImageUrl) ? (string)$this->xmlData->MainImageUrl : false;
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
     * @return int
     */
    public function GetQuantity(){
        $value = isset($this->xmlData->Quantity) ? (string)$this->xmlData->Quantity : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return long
     */
    public function GetCategoryId(){
        $value = isset($this->xmlData->CategoryId) ? (string)$this->xmlData->CategoryId : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiWarehouseVendorInfo
     */
    public function GetVendor(){
        $value = isset($this->xmlData->Vendor) ? $this->xmlData->Vendor : false;
        return new OtapiWarehouseVendorInfo($value);
    }
    /**
     * @return decimal
     */
    public function GetWeight(){
        $value = isset($this->xmlData->Weight) ? (string)$this->xmlData->Weight : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}