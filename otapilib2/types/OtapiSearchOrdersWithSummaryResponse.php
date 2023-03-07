<?php

class OtapiSearchOrdersWithSummaryResponse extends BaseOtapiType{
    /**
     * @return OtapiOrderListFrameWithSummaryAnswer
     */
    public function GetSearchOrdersWithSummaryResult(){
        $value = isset($this->xmlData->SearchOrdersWithSummaryResult) ? $this->xmlData->SearchOrdersWithSummaryResult : false;
        return new OtapiOrderListFrameWithSummaryAnswer($value);
    }
}