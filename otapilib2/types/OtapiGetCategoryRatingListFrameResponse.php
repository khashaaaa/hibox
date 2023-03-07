<?php

class OtapiGetCategoryRatingListFrameResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryListFrameAnswer
     */
    public function GetGetCategoryRatingListFrameResult(){
        $value = isset($this->xmlData->GetCategoryRatingListFrameResult) ? $this->xmlData->GetCategoryRatingListFrameResult : false;
        return new OtapiCategoryListFrameAnswer($value);
    }
}