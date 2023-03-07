<?php

class OtapiSearchRatingListCategoriesResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryListFrameAnswer
     */
    public function GetSearchRatingListCategoriesResult(){
        $value = isset($this->xmlData->SearchRatingListCategoriesResult) ? $this->xmlData->SearchRatingListCategoriesResult : false;
        return new OtapiCategoryListFrameAnswer($value);
    }
}