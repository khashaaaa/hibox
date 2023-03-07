<?php

class OtapiGetItemFullInfoWithPromotionsResponse extends BaseOtapiType{
    /**
     * @return OtapiItemFullInfoAnswer
     */
    public function GetGetItemFullInfoWithPromotionsResult(){
        $value = isset($this->xmlData->GetItemFullInfoWithPromotionsResult) ? $this->xmlData->GetItemFullInfoWithPromotionsResult : false;
        return new OtapiItemFullInfoAnswer($value);
    }
}