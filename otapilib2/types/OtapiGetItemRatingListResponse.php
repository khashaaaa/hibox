<?php

class OtapiGetItemRatingListResponse extends BaseOtapiType{
    /**
     * @return OtapiItemInfoRatingListAnswer
     */
    public function GetGetItemRatingListResult(){
        $value = isset($this->xmlData->GetItemRatingListResult) ? $this->xmlData->GetItemRatingListResult : false;
        return new OtapiItemInfoRatingListAnswer($value);
    }
}