<?php

class RatingListSearchResultOfOtapiBrandInfo extends OtapiRatingListSearchResult{
    /**
     * @return DataSubListOfOtapiBrandInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataSubListOfOtapiBrandInfo($value);
    }
}