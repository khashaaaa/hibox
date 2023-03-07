<?php

class OtapiSearchRatingListItemsResponse extends BaseOtapiType{
    /**
     * @return OtapiItemInfoListFrameAnswer
     */
    public function GetSearchRatingListItemsResult(){
        $value = isset($this->xmlData->SearchRatingListItemsResult) ? $this->xmlData->SearchRatingListItemsResult : false;
        return new OtapiItemInfoListFrameAnswer($value);
    }
}