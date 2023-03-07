<?php

class OtapiGetBrandRatingListFrameResponse extends BaseOtapiType{
    /**
     * @return OtapiBrandInfoListFrameAnswer
     */
    public function GetGetBrandRatingListFrameResult(){
        $value = isset($this->xmlData->GetBrandRatingListFrameResult) ? $this->xmlData->GetBrandRatingListFrameResult : false;
        return new OtapiBrandInfoListFrameAnswer($value);
    }
}