<?php

class OtapiRemoveUserFromDiscountGroupResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetRemoveUserFromDiscountGroupResult(){
        $value = isset($this->xmlData->RemoveUserFromDiscountGroupResult) ? $this->xmlData->RemoveUserFromDiscountGroupResult : false;
        return new VoidOtapiAnswer($value);
    }
}