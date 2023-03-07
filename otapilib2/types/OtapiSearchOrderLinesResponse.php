<?php

class OtapiSearchOrderLinesResponse extends BaseOtapiType{
    /**
     * @return OtapiOrderLineInfoListFrameAnswer
     */
    public function GetSearchOrderLinesResult(){
        $value = isset($this->xmlData->SearchOrderLinesResult) ? $this->xmlData->SearchOrderLinesResult : false;
        return new OtapiOrderLineInfoListFrameAnswer($value);
    }
}