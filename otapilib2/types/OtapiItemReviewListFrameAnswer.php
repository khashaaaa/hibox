<?php

class OtapiItemReviewListFrameAnswer extends OtapiAnswer{
    /**
     * @return DataSubListOfOtapiItemReview
     */
    public function GetTradeRateInfoList(){
        $value = isset($this->xmlData->TradeRateInfoList) ? $this->xmlData->TradeRateInfoList : false;
        return new DataSubListOfOtapiItemReview($value);
    }
}