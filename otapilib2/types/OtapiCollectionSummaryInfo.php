<?php

class OtapiCollectionSummaryInfo extends BaseOtapiType{
    /**
     * @return int
     */
    public function GetTotalCount(){
        $value = isset($this->xmlData->TotalCount) ? (string)$this->xmlData->TotalCount : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiBasePrice
     */
    public function GetTotalPrice(){
        $value = isset($this->xmlData->TotalPrice) ? $this->xmlData->TotalPrice : false;
        return new OtapiBasePrice($value);
    }
}