<?php

class OtapiItemSearchResultAnswer extends OtapiAnswer{
    /**
     * @return OtapiItemSearchResult
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiItemSearchResult($value);
    }
}