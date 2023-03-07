<?php

class OtapiFinancialCalculationReport extends OtapiDataListOfFinancialCalculationItem{
    /**
     * @return string
     */
    public function GetInternalCurrencyCode(){
        $value = isset($this->xmlData->InternalCurrencyCode) ? (string)$this->xmlData->InternalCurrencyCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}