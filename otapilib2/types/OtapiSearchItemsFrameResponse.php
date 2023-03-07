<?php

class OtapiSearchItemsFrameResponse extends BaseOtapiType{
    /**
     * @return OtapiItemSearchResultAnswer
     */
    public function GetSearchItemsFrameResult(){
        $value = isset($this->xmlData->SearchItemsFrameResult) ? $this->xmlData->SearchItemsFrameResult : false;
        return new OtapiItemSearchResultAnswer($value);
    }
}