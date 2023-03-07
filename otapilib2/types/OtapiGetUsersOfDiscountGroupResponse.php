<?php

class OtapiGetUsersOfDiscountGroupResponse extends BaseOtapiType{
    /**
     * @return OtapiDiscountGroupUserInfoListFrameAnswer
     */
    public function GetGetUsersOfDiscountGroupResult(){
        $value = isset($this->xmlData->GetUsersOfDiscountGroupResult) ? $this->xmlData->GetUsersOfDiscountGroupResult : false;
        return new OtapiDiscountGroupUserInfoListFrameAnswer($value);
    }
}