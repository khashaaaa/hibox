<?php

class OtapiBatchItemSearchResult extends OtapiBatchResultOfGeneralErrorCode{
    /**
     * @return OtapiItemSearchResult
     */
    public function GetItems(){
        $value = isset($this->xmlData->Items) ? $this->xmlData->Items : false;
        return new OtapiItemSearchResult($value);
    }
    /**
     * @return OtapiBatchErrorInfoOfGeneralErrorCode
     */
    public function GetItemsError(){
        $value = isset($this->xmlData->ItemsError) ? $this->xmlData->ItemsError : false;
        return new OtapiBatchErrorInfoOfGeneralErrorCode($value);
    }
    /**
     * @return DataListOfOtapiCategory
     */
    public function GetSubCategories(){
        $value = isset($this->xmlData->SubCategories) ? $this->xmlData->SubCategories : false;
        return new DataListOfOtapiCategory($value);
    }
    /**
     * @return OtapiBatchErrorInfoOfGeneralErrorCode
     */
    public function GetSubCategoriesError(){
        $value = isset($this->xmlData->SubCategoriesError) ? $this->xmlData->SubCategoriesError : false;
        return new OtapiBatchErrorInfoOfGeneralErrorCode($value);
    }
    /**
     * @return DataListOfOtapiCategory
     */
    public function GetRootPath(){
        $value = isset($this->xmlData->RootPath) ? $this->xmlData->RootPath : false;
        return new DataListOfOtapiCategory($value);
    }
    /**
     * @return OtapiBatchErrorInfoOfGeneralErrorCode
     */
    public function GetRootPathError(){
        $value = isset($this->xmlData->RootPathError) ? $this->xmlData->RootPathError : false;
        return new OtapiBatchErrorInfoOfGeneralErrorCode($value);
    }
    /**
     * @return DataListOfOtapiItemSearchProperty
     */
    public function GetSearchProperties(){
        $value = isset($this->xmlData->SearchProperties) ? $this->xmlData->SearchProperties : false;
        return new DataListOfOtapiItemSearchProperty($value);
    }
    /**
     * @return OtapiBatchErrorInfoOfGeneralErrorCode
     */
    public function GetSearchPropertiesError(){
        $value = isset($this->xmlData->SearchPropertiesError) ? $this->xmlData->SearchPropertiesError : false;
        return new OtapiBatchErrorInfoOfGeneralErrorCode($value);
    }
    /**
     * @return OtapiVendorInfo
     */
    public function GetVendor(){
        $value = isset($this->xmlData->Vendor) ? $this->xmlData->Vendor : false;
        return new OtapiVendorInfo($value);
    }
    /**
     * @return OtapiBatchErrorInfoOfGeneralErrorCode
     */
    public function GetVendorError(){
        $value = isset($this->xmlData->VendorError) ? $this->xmlData->VendorError : false;
        return new OtapiBatchErrorInfoOfGeneralErrorCode($value);
    }
    /**
     * @return OtapiBrandInfo
     */
    public function GetBrand(){
        $value = isset($this->xmlData->Brand) ? $this->xmlData->Brand : false;
        return new OtapiBrandInfo($value);
    }
    /**
     * @return OtapiBatchErrorInfoOfGeneralErrorCode
     */
    public function GetBrandError(){
        $value = isset($this->xmlData->BrandError) ? $this->xmlData->BrandError : false;
        return new OtapiBatchErrorInfoOfGeneralErrorCode($value);
    }
    /**
     * @return OtapiCategory
     */
    public function GetCategory(){
        $value = isset($this->xmlData->Category) ? $this->xmlData->Category : false;
        return new OtapiCategory($value);
    }
    /**
     * @return OtapiBatchErrorInfoOfGeneralErrorCode
     */
    public function GetCategoryError(){
        $value = isset($this->xmlData->CategoryError) ? $this->xmlData->CategoryError : false;
        return new OtapiBatchErrorInfoOfGeneralErrorCode($value);
    }
    /**
     * @return DataListOfOtapiCategory
     */
    public function GetHintCategories(){
        $value = isset($this->xmlData->HintCategories) ? $this->xmlData->HintCategories : false;
        return new DataListOfOtapiCategory($value);
    }
    /**
     * @return OtapiBatchErrorInfoOfGeneralErrorCode
     */
    public function GetHintCategoriesError(){
        $value = isset($this->xmlData->HintCategoriesError) ? $this->xmlData->HintCategoriesError : false;
        return new OtapiBatchErrorInfoOfGeneralErrorCode($value);
    }
    /**
     * @return OtapiDataListOfProviderSearchMethodInfo
     */
    public function GetAvailableSearchMethods(){
        $value = isset($this->xmlData->AvailableSearchMethods) ? $this->xmlData->AvailableSearchMethods : false;
        return new OtapiDataListOfProviderSearchMethodInfo($value);
    }
    /**
     * @return OtapiBatchErrorInfoOfGeneralErrorCode
     */
    public function GetAvailableSearchMethodsError(){
        $value = isset($this->xmlData->AvailableSearchMethodsError) ? $this->xmlData->AvailableSearchMethodsError : false;
        return new OtapiBatchErrorInfoOfGeneralErrorCode($value);
    }
}