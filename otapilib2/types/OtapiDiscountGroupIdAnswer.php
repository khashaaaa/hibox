<?php

class OtapiDiscountGroupIdAnswer extends OtapiAnswer{
    /**
     * @return OtapiDiscountGroupId
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDiscountGroupId($value);
    }
}