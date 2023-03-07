<?php

class OtapiGetTradeRateInfoListFrameResponse extends BaseOtapiType{
    /**
     * @return OtapiItemReviewListFrameAnswer
     */
    public function GetGetTradeRateInfoListFrameResult(){
        $value = isset($this->xmlData->GetTradeRateInfoListFrameResult) ? $this->xmlData->GetTradeRateInfoListFrameResult : false;
        return new OtapiItemReviewListFrameAnswer($value);
    }
}