<?php

class OtapiRemoveDiscountGroupFromInstanceResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetRemoveDiscountGroupFromInstanceResult(){
        $value = isset($this->xmlData->RemoveDiscountGroupFromInstanceResult) ? $this->xmlData->RemoveDiscountGroupFromInstanceResult : false;
        return new VoidOtapiAnswer($value);
    }
}