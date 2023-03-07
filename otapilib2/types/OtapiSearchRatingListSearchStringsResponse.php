<?php

class OtapiSearchRatingListSearchStringsResponse extends BaseOtapiType{
    /**
     * @return OtapiSearchRatingListSearchStringsAnswer
     */
    public function GetSearchRatingListSearchStringsResult(){
        $value = isset($this->xmlData->SearchRatingListSearchStringsResult) ? $this->xmlData->SearchRatingListSearchStringsResult : false;
        return new OtapiSearchRatingListSearchStringsAnswer($value);
    }
}