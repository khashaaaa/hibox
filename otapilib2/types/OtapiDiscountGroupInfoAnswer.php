<?php

class OtapiDiscountGroupInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiDiscountGroupInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDiscountGroupInfo($value);
    }
}