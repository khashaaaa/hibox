<?php

class OtapiBatchRatingListsSearchResult extends BaseOtapiType{
    /**
     * @return ArrayOfRatingListSearchResultOfOtapiItemInfo
     */
    public function GetItems(){
        $value = isset($this->xmlData->Items) ? $this->xmlData->Items : false;
        return new ArrayOfRatingListSearchResultOfOtapiItemInfo($value);
    }
    /**
     * @return ArrayOfRatingListSearchResultOfOtapiVendorInfo
     */
    public function GetVendors(){
        $value = isset($this->xmlData->Vendors) ? $this->xmlData->Vendors : false;
        return new ArrayOfRatingListSearchResultOfOtapiVendorInfo($value);
    }
    /**
     * @return ArrayOfRatingListSearchResultOfOtapiCategory
     */
    public function GetCategories(){
        $value = isset($this->xmlData->Categories) ? $this->xmlData->Categories : false;
        return new ArrayOfRatingListSearchResultOfOtapiCategory($value);
    }
    /**
     * @return ArrayOfRatingListSearchResultOfOtapiBrandInfo
     */
    public function GetBrands(){
        $value = isset($this->xmlData->Brands) ? $this->xmlData->Brands : false;
        return new ArrayOfRatingListSearchResultOfOtapiBrandInfo($value);
    }
    /**
     * @return OtapiArrayOfRatingListSearchResultOfString
     */
    public function GetSearchStrings(){
        $value = isset($this->xmlData->SearchStrings) ? $this->xmlData->SearchStrings : false;
        return new OtapiArrayOfRatingListSearchResultOfString($value);
    }
}