<?php

class OtapiWarehouseVendorInfo extends BaseOtapiType{
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
    public function GetEmail(){
        $value = isset($this->xmlData->Email) ? (string)$this->xmlData->Email : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPictureUrl(){
        $value = isset($this->xmlData->PictureUrl) ? (string)$this->xmlData->PictureUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLocation(){
        $value = isset($this->xmlData->Location) ? (string)$this->xmlData->Location : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}