<?php

class OtapiFinancialReport extends BaseOtapiType{
    /**
     * @return OtapiMetaInfo
     */
    public function GetMetaInfo(){
        $value = isset($this->xmlData->MetaInfo) ? $this->xmlData->MetaInfo : false;
        return new OtapiMetaInfo($value);
    }
    /**
     * @return dateTime
     */
    public function GetReportTime(){
        $value = isset($this->xmlData->ReportTime) ? (string)$this->xmlData->ReportTime : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiFinancialReportBalanceInfo
     */
    public function GetBalance(){
        $value = isset($this->xmlData->Balance) ? $this->xmlData->Balance : false;
        return new OtapiFinancialReportBalanceInfo($value);
    }
}