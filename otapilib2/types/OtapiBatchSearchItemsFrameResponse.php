<?php

class OtapiBatchSearchItemsFrameResponse extends BaseOtapiType{
    /**
     * @return OtapiBatchItemSearchResultAnswer
     */
    public function GetBatchSearchItemsFrameResult(){
        $value = isset($this->xmlData->BatchSearchItemsFrameResult) ? $this->xmlData->BatchSearchItemsFrameResult : false;
        return new OtapiBatchItemSearchResultAnswer($value);
    }
}