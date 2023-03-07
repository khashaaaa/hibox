<?php

class OtapiFindDiscountGroupResponse extends BaseOtapiType{
    /**
     * @return OtapiDiscountGroupInfoAnswer
     */
    public function GetFindDiscountGroupResult(){
        $value = isset($this->xmlData->FindDiscountGroupResult) ? $this->xmlData->FindDiscountGroupResult : false;
        return new OtapiDiscountGroupInfoAnswer($value);
    }
}