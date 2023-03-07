<?php

class OtapiGetBrandRatingListResponse extends BaseOtapiType{
    /**
     * @return OtapiBrandInfoListAnswer
     */
    public function GetGetBrandRatingListResult(){
        $value = isset($this->xmlData->GetBrandRatingListResult) ? $this->xmlData->GetBrandRatingListResult : false;
        return new OtapiBrandInfoListAnswer($value);
    }
}