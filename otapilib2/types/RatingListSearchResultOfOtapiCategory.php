<?php

class RatingListSearchResultOfOtapiCategory extends OtapiRatingListSearchResult{
    /**
     * @return DataSubListOfOtapiCategory
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataSubListOfOtapiCategory($value);
    }
}