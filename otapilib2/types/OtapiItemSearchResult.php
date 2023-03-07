<?php

class OtapiItemSearchResult extends BaseOtapiType{
    /**
     * @return DataSubListOfOtapiItemInfo
     */
    public function GetItems(){
        $value = isset($this->xmlData->Items) ? $this->xmlData->Items : false;
        return new DataSubListOfOtapiItemInfo($value);
    }
    /**
     * @return DataListOfOtapiSearchCategoryInfo
     */
    public function GetCategories(){
        $value = isset($this->xmlData->Categories) ? $this->xmlData->Categories : false;
        return new DataListOfOtapiSearchCategoryInfo($value);
    }
    /**
     * @return DataListOfOtapiSearchBrandInfo
     */
    public function GetBrands(){
        $value = isset($this->xmlData->Brands) ? $this->xmlData->Brands : false;
        return new DataListOfOtapiSearchBrandInfo($value);
    }
    /**
     * @return string
     */
    public function GetTranslatedItemTitle(){
        $value = isset($this->xmlData->TranslatedItemTitle) ? (string)$this->xmlData->TranslatedItemTitle : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCurrentImageUrl(){
        $value = isset($this->xmlData->CurrentImageUrl) ? (string)$this->xmlData->CurrentImageUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
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
    public function GetCurrentSort(){
        $value = isset($this->xmlData->CurrentSort) ? (string)$this->xmlData->CurrentSort : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetCurrentFrameSize(){
        $value = isset($this->xmlData->CurrentFrameSize) ? (string)$this->xmlData->CurrentFrameSize : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetMaximumPageCount(){
        $value = isset($this->xmlData->MaximumPageCount) ? (string)$this->xmlData->MaximumPageCount : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsFoundByItemId(){
        $value = isset($this->xmlData->IsFoundByItemId) ? (string)$this->xmlData->IsFoundByItemId : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}