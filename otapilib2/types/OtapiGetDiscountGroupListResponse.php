<?php

class OtapiGetDiscountGroupListResponse extends BaseOtapiType{
    /**
     * @return OtapiDiscountGroupInfoListAnswer
     */
    public function GetGetDiscountGroupListResult(){
        $value = isset($this->xmlData->GetDiscountGroupListResult) ? $this->xmlData->GetDiscountGroupListResult : false;
        return new OtapiDiscountGroupInfoListAnswer($value);
    }
}