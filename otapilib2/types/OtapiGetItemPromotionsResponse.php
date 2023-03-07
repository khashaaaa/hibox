<?php

class OtapiGetItemPromotionsResponse extends BaseOtapiType{
    /**
     * @return OtapiItemPromotionListAnswer
     */
    public function GetGetItemPromotionsResult(){
        $value = isset($this->xmlData->GetItemPromotionsResult) ? $this->xmlData->GetItemPromotionsResult : false;
        return new OtapiItemPromotionListAnswer($value);
    }
}