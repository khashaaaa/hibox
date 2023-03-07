<?php

class OtapiTimePeriodCallStatistics extends BaseOtapiType{
    /**
     * @return long
     */
    public function GetDailyCallCount(){
        $value = isset($this->xmlData->DailyCallCount) ? (string)$this->xmlData->DailyCallCount : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return long
     */
    public function GetWeeklyCallCount(){
        $value = isset($this->xmlData->WeeklyCallCount) ? (string)$this->xmlData->WeeklyCallCount : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return long
     */
    public function GetMonthlyCallCount(){
        $value = isset($this->xmlData->MonthlyCallCount) ? (string)$this->xmlData->MonthlyCallCount : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}