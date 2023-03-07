<?php

class OtapiDataListOfFinancialCalculationItem extends BaseOtapiType{
    /**
     * @return OtapiArrayOfFinancialCalculationItem
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfFinancialCalculationItem($value);
    }
}