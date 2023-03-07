<?php

class OtapiOrderListFrameWithSummaryAnswer extends OtapiAnswer{
    /**
     * @return OtapiOrderListFrameWithSummary
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiOrderListFrameWithSummary($value);
    }
}