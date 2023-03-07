<?php

class OtapiGetDiscountGroupResponse extends BaseOtapiType{
    /**
     * @return OtapiDiscountGroupInfoAnswer
     */
    public function GetGetDiscountGroupResult(){
        $value = isset($this->xmlData->GetDiscountGroupResult) ? $this->xmlData->GetDiscountGroupResult : false;
        return new OtapiDiscountGroupInfoAnswer($value);
    }
}