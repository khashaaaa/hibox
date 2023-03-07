<?php

class RatingListSearchResultOfOtapiItemInfo extends OtapiRatingListSearchResult{
    /**
     * @return DataSubListOfOtapiItemInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataSubListOfOtapiItemInfo($value);
    }
}