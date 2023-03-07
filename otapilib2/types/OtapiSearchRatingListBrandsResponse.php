<?php

class OtapiSearchRatingListBrandsResponse extends BaseOtapiType{
    /**
     * @return OtapiBrandInfoListFrameAnswer
     */
    public function GetSearchRatingListBrandsResult(){
        $value = isset($this->xmlData->SearchRatingListBrandsResult) ? $this->xmlData->SearchRatingListBrandsResult : false;
        return new OtapiBrandInfoListFrameAnswer($value);
    }
}