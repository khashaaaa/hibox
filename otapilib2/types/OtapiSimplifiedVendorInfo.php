<?php

class OtapiSimplifiedVendorInfo extends OtapiSimplifiedEntity{
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
    public function GetDisplayName(){
        $value = isset($this->xmlData->DisplayName) ? (string)$this->xmlData->DisplayName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetShopName(){
        $value = isset($this->xmlData->ShopName) ? (string)$this->xmlData->ShopName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetSex(){
        $value = isset($this->xmlData->Sex) ? (string)$this->xmlData->Sex : false;
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
    public function GetDisplayPictureUrl(){
        $value = isset($this->xmlData->DisplayPictureUrl) ? (string)$this->xmlData->DisplayPictureUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiSimplifiedLocation
     */
    public function GetLocation(){
        $value = isset($this->xmlData->Location) ? $this->xmlData->Location : false;
        return new OtapiSimplifiedLocation($value);
    }
    /**
     * @return OtapiArrayOfSimplifiedValueWithId
     */
    public function GetFeatures(){
        $value = isset($this->xmlData->Features) ? $this->xmlData->Features : false;
        return new OtapiArrayOfSimplifiedValueWithId($value);
    }
}