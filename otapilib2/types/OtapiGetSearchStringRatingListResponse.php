<?php

class OtapiGetSearchStringRatingListResponse extends BaseOtapiType{
    /**
     * @return OtapiSearchStringRatingListAnswer
     */
    public function GetGetSearchStringRatingListResult(){
        $value = isset($this->xmlData->GetSearchStringRatingListResult) ? $this->xmlData->GetSearchStringRatingListResult : false;
        return new OtapiSearchStringRatingListAnswer($value);
    }
}