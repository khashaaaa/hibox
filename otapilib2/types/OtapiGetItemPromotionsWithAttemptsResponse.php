<?php

class OtapiGetItemPromotionsWithAttemptsResponse extends BaseOtapiType{
    /**
     * @return OtapiItemPromotionListAnswer
     */
    public function GetGetItemPromotionsWithAttemptsResult(){
        $value = isset($this->xmlData->GetItemPromotionsWithAttemptsResult) ? $this->xmlData->GetItemPromotionsWithAttemptsResult : false;
        return new OtapiItemPromotionListAnswer($value);
    }
}