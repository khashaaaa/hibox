<?php

class OtapiDiscountGroupInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfDiscountGroupInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfDiscountGroupInfo($value);
    }
}