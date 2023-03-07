<?php

class OtapiGetVendorRatingListFrameResponse extends BaseOtapiType{
    /**
     * @return OtapiVendorInfoListFrameAnswer
     */
    public function GetGetVendorRatingListFrameResult(){
        $value = isset($this->xmlData->GetVendorRatingListFrameResult) ? $this->xmlData->GetVendorRatingListFrameResult : false;
        return new OtapiVendorInfoListFrameAnswer($value);
    }
}