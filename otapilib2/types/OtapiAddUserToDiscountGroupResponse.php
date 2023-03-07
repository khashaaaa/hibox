<?php

class OtapiAddUserToDiscountGroupResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetAddUserToDiscountGroupResult(){
        $value = isset($this->xmlData->AddUserToDiscountGroupResult) ? $this->xmlData->AddUserToDiscountGroupResult : false;
        return new VoidOtapiAnswer($value);
    }
}