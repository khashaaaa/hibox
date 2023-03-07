<?php

class OtapiUpdateDiscountGroupResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateDiscountGroupResult(){
        $value = isset($this->xmlData->UpdateDiscountGroupResult) ? $this->xmlData->UpdateDiscountGroupResult : false;
        return new VoidOtapiAnswer($value);
    }
}