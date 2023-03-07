<?php

class OtapiRatingListSearchResultOfString extends OtapiRatingListSearchResult{
    /**
     * @return OtapiDataSubListOfString
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataSubListOfString($value);
    }
}