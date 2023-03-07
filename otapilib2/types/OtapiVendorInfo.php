<?php

class OtapiVendorInfo extends OtapiEntity{
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
     * @return OtapiLocation
     */
    public function GetLocation(){
        $value = isset($this->xmlData->Location) ? $this->xmlData->Location : false;
        return new OtapiLocation($value);
    }
    /**
     * @return OtapiVendorRating
     */
    public function GetCredit(){
        $value = isset($this->xmlData->Credit) ? $this->xmlData->Credit : false;
        return new OtapiVendorRating($value);
    }
    /**
     * @return OtapiVendorScores
     */
    public function GetScores(){
        $value = isset($this->xmlData->Scores) ? $this->xmlData->Scores : false;
        return new OtapiVendorScores($value);
    }
    /**
     * @return OtapiArrayOfString1
     */
    public function GetFeatures(){
        $value = isset($this->xmlData->Features) ? $this->xmlData->Features : false;
        return new OtapiArrayOfString1($value);
    }
    /**
     * @return OtapiArrayOfFeaturedValue
     */
    public function GetFeaturedValues(){
        $value = isset($this->xmlData->FeaturedValues) ? $this->xmlData->FeaturedValues : false;
        return new OtapiArrayOfFeaturedValue($value);
    }
}