<?php

class OtapiGetVendorRatingListResponse extends BaseOtapiType{
    /**
     * @return OtapiVendorInfoListAnswer
     */
    public function GetGetVendorRatingListResult(){
        $value = isset($this->xmlData->GetVendorRatingListResult) ? $this->xmlData->GetVendorRatingListResult : false;
        return new OtapiVendorInfoListAnswer($value);
    }
}