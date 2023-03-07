<?php

class OtapiComponentCallStatistics extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetName(){
        $value = isset($this->xmlData->Name) ? (string)$this->xmlData->Name : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiTimePeriodCallStatistics
     */
    public function GetStatisticsByTimePeriod(){
        $value = isset($this->xmlData->StatisticsByTimePeriod) ? $this->xmlData->StatisticsByTimePeriod : false;
        return new OtapiTimePeriodCallStatistics($value);
    }
    /**
     * @return long
     */
    public function GetTotalCount(){
        $value = isset($this->xmlData->TotalCount) ? (string)$this->xmlData->TotalCount : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}