<?php

class OtapiSearchRatingListVendorsResponse extends BaseOtapiType{
    /**
     * @return OtapiVendorInfoListFrameAnswer
     */
    public function GetSearchRatingListVendorsResult(){
        $value = isset($this->xmlData->SearchRatingListVendorsResult) ? $this->xmlData->SearchRatingListVendorsResult : false;
        return new OtapiVendorInfoListFrameAnswer($value);
    }
}