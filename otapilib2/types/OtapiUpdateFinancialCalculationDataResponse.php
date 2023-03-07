<?php

class OtapiUpdateFinancialCalculationDataResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateFinancialCalculationDataResult(){
        $value = isset($this->xmlData->UpdateFinancialCalculationDataResult) ? $this->xmlData->UpdateFinancialCalculationDataResult : false;
        return new VoidOtapiAnswer($value);
    }
}