<?php

class OtapiBatchRatingListsSearchResultAnswer extends OtapiAnswer{
    /**
     * @return OtapiBatchRatingListsSearchResult
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiBatchRatingListsSearchResult($value);
    }
}