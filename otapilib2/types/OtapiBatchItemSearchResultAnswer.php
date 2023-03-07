<?php

class OtapiBatchItemSearchResultAnswer extends OtapiAnswer{
    /**
     * @return OtapiBatchItemSearchResult
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiBatchItemSearchResult($value);
    }
}