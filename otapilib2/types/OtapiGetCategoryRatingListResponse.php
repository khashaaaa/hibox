<?php

class OtapiGetCategoryRatingListResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryListAnswer
     */
    public function GetGetCategoryRatingListResult(){
        $value = isset($this->xmlData->GetCategoryRatingListResult) ? $this->xmlData->GetCategoryRatingListResult : false;
        return new OtapiCategoryListAnswer($value);
    }
}