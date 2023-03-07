<?php

class OtapiProviderSearchMethodInfo extends BaseOtapiType{
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
    public function GetDisplayName(){
        $value = isset($this->xmlData->DisplayName) ? (string)$this->xmlData->DisplayName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfProviderSearchFlag
     */
    public function GetFlags(){
        $value = isset($this->xmlData->Flags) ? $this->xmlData->Flags : false;
        return new OtapiArrayOfProviderSearchFlag($value);
    }
    /**
     * @return int
     */
    public function GetOptimalFrameSize(){
        $value = isset($this->xmlData->OptimalFrameSize) ? (string)$this->xmlData->OptimalFrameSize : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetMaximumFrameSize(){
        $value = isset($this->xmlData->MaximumFrameSize) ? (string)$this->xmlData->MaximumFrameSize : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetMaximumItemsCount(){
        $value = isset($this->xmlData->MaximumItemsCount) ? (string)$this->xmlData->MaximumItemsCount : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfProviderSearchSortInfo
     */
    public function GetAvailableSorts(){
        $value = isset($this->xmlData->AvailableSorts) ? $this->xmlData->AvailableSorts : false;
        return new OtapiArrayOfProviderSearchSortInfo($value);
    }
    /**
     * @return boolean
     */
    public function Text(){
        $value = isset($this->xmlData->Text) ? (string)$this->xmlData->Text : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function Category(){
        $value = isset($this->xmlData->Category) ? (string)$this->xmlData->Category : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function Vendor(){
        $value = isset($this->xmlData->Vendor) ? (string)$this->xmlData->Vendor : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function VendorLocation(){
        $value = isset($this->xmlData->VendorLocation) ? (string)$this->xmlData->VendorLocation : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function Brand(){
        $value = isset($this->xmlData->Brand) ? (string)$this->xmlData->Brand : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function Configurators(){
        $value = isset($this->xmlData->Configurators) ? (string)$this->xmlData->Configurators : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetMultipleConfiguratorLogic(){
        $value = isset($this->xmlData->MultipleConfiguratorLogic) ? (string)$this->xmlData->MultipleConfiguratorLogic : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function PriceRange(){
        $value = isset($this->xmlData->PriceRange) ? (string)$this->xmlData->PriceRange : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function VendorRatingRange(){
        $value = isset($this->xmlData->VendorRatingRange) ? (string)$this->xmlData->VendorRatingRange : false;
        $propertyType = 'boolean';
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
     * @return boolean
     */
    public function VolumeRange(){
        $value = isset($this->xmlData->VolumeRange) ? (string)$this->xmlData->VolumeRange : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetVolumeRangeLogic(){
        $value = isset($this->xmlData->VolumeRangeLogic) ? (string)$this->xmlData->VolumeRangeLogic : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function StuffStatus(){
        $value = isset($this->xmlData->StuffStatus) ? (string)$this->xmlData->StuffStatus : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfProviderSearchFeatureInfo
     */
    public function GetFeatures(){
        $value = isset($this->xmlData->Features) ? $this->xmlData->Features : false;
        return new OtapiArrayOfProviderSearchFeatureInfo($value);
    }
    /**
     * @return boolean
     */
    public function FirstLotRange(){
        $value = isset($this->xmlData->FirstLotRange) ? (string)$this->xmlData->FirstLotRange : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetFirstLotRangeLogic(){
        $value = isset($this->xmlData->FirstLotRangeLogic) ? (string)$this->xmlData->FirstLotRangeLogic : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function EnableMorphologyPreprocess(){
        $value = isset($this->xmlData->EnableMorphologyPreprocess) ? (string)$this->xmlData->EnableMorphologyPreprocess : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetModule(){
        $value = isset($this->xmlData->Module) ? (string)$this->xmlData->Module : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function AllowSearchWithoutParameters(){
        $value = isset($this->xmlData->AllowSearchWithoutParameters) ? (string)$this->xmlData->AllowSearchWithoutParameters : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}