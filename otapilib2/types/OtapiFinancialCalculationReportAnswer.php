<?php

class OtapiFinancialCalculationReportAnswer extends OtapiAnswer{
    /**
     * @return OtapiFinancialCalculationReport
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiFinancialCalculationReport($value);
    }
}