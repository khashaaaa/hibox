<?php

class OtapiItemPromotionListAnswer extends OtapiAnswer{
    /**
     * @return DataListOfOtapiItemPromotion
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataListOfOtapiItemPromotion($value);
    }
}