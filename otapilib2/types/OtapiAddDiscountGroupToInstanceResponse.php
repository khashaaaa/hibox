<?php

class OtapiAddDiscountGroupToInstanceResponse extends BaseOtapiType{
    /**
     * @return OtapiDiscountGroupIdAnswer
     */
    public function GetAddDiscountGroupToInstanceResult(){
        $value = isset($this->xmlData->AddDiscountGroupToInstanceResult) ? $this->xmlData->AddDiscountGroupToInstanceResult : false;
        return new OtapiDiscountGroupIdAnswer($value);
    }
}