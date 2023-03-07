<?php

class OtapiGetItemRatingListFrameResponse extends BaseOtapiType{
    /**
     * @return OtapiItemInfoListFrameAnswer
     */
    public function GetGetItemRatingListFrameResult(){
        $value = isset($this->xmlData->GetItemRatingListFrameResult) ? $this->xmlData->GetItemRatingListFrameResult : false;
        return new OtapiItemInfoListFrameAnswer($value);
    }
}