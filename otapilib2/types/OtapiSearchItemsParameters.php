<?php

class OtapiSearchItemsParameters extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetProvider(){
        $value = isset($this->xmlData->Provider) ? (string)$this->xmlData->Provider : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetSearchMethod(){
        $value = isset($this->xmlData->SearchMethod) ? (string)$this->xmlData->SearchMethod : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCategoryId(){
        $value = isset($this->xmlData->CategoryId) ? (string)$this->xmlData->CategoryId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetVendorName(){
        $value = isset($this->xmlData->VendorName) ? (string)$this->xmlData->VendorName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetVendorId(){
        $value = isset($this->xmlData->VendorId) ? (string)$this->xmlData->VendorId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetVendorAreaId(){
        $value = isset($this->xmlData->VendorAreaId) ? (string)$this->xmlData->VendorAreaId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetItemTitle(){
        $value = isset($this->xmlData->ItemTitle) ? (string)$this->xmlData->ItemTitle : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLanguageOfQuery(){
        $value = isset($this->xmlData->LanguageOfQuery) ? (string)$this->xmlData->LanguageOfQuery : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetMinPrice(){
        $value = isset($this->xmlData->MinPrice) ? (string)$this->xmlData->MinPrice : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetMaxPrice(){
        $value = isset($this->xmlData->MaxPrice) ? (string)$this->xmlData->MaxPrice : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return long
     */
    public function GetMinVolume(){
        $value = isset($this->xmlData->MinVolume) ? (string)$this->xmlData->MinVolume : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return long
     */
    public function GetMaxVolume(){
        $value = isset($this->xmlData->MaxVolume) ? (string)$this->xmlData->MaxVolume : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetMinVendorRating(){
        $value = isset($this->xmlData->MinVendorRating) ? (string)$this->xmlData->MinVendorRating : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetMaxVendorRating(){
        $value = isset($this->xmlData->MaxVendorRating) ? (string)$this->xmlData->MaxVendorRating : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetBrandId(){
        $value = isset($this->xmlData->BrandId) ? (string)$this->xmlData->BrandId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetBrandPropertyValueId(){
        $value = isset($this->xmlData->BrandPropertyValueId) ? (string)$this->xmlData->BrandPropertyValueId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return ArrayOfOtapiValuedConfigurator1
     */
    public function GetConfigurators(){
        $value = isset($this->xmlData->Configurators) ? $this->xmlData->Configurators : false;
        return new ArrayOfOtapiValuedConfigurator1($value);
    }
    /**
     * @return string
     */
    public function GetOrderBy(){
        $value = isset($this->xmlData->OrderBy) ? (string)$this->xmlData->OrderBy : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetOutputMode(){
        $value = isset($this->xmlData->OutputMode) ? (string)$this->xmlData->OutputMode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCategoryMode(){
        $value = isset($this->xmlData->CategoryMode) ? (string)$this->xmlData->CategoryMode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsOriginal(){
        $value = isset($this->xmlData->IsOriginal) ? (string)$this->xmlData->IsOriginal : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsTmall(){
        $value = isset($this->xmlData->IsTmall) ? (string)$this->xmlData->IsTmall : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetStuffStatus(){
        $value = isset($this->xmlData->StuffStatus) ? (string)$this->xmlData->StuffStatus : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return ArrayOfOtapiSearchFeature
     */
    public function GetFeatures(){
        $value = isset($this->xmlData->Features) ? $this->xmlData->Features : false;
        return new ArrayOfOtapiSearchFeature($value);
    }
    /**
     * @return boolean
     */
    public function IsClearItemTitles(){
        $value = isset($this->xmlData->IsClearItemTitles) ? (string)$this->xmlData->IsClearItemTitles : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}