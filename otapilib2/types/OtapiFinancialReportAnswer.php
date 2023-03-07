<?php

class OtapiFinancialReportAnswer extends OtapiAnswer{
    /**
     * @return OtapiFinancialReport
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiFinancialReport($value);
    }
}