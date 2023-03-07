<?php

class OtapiCallStatistics extends BaseOtapiType{
    /**
     * @return long
     */
    public function GetCallCount(){
        $value = isset($this->xmlData->CallCount) ? (string)$this->xmlData->CallCount : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
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
    /**
     * @return OtapiArrayOfInt
     */
    public function GetLastMinuteCallCounts(){
        $value = isset($this->xmlData->LastMinuteCallCounts) ? $this->xmlData->LastMinuteCallCounts : false;
        return new OtapiArrayOfInt($value);
    }
    /**
     * @return int
     */
    public function GetActiveInstances(){
        $value = isset($this->xmlData->ActiveInstances) ? (string)$this->xmlData->ActiveInstances : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetActiveTestInstances(){
        $value = isset($this->xmlData->ActiveTestInstances) ? (string)$this->xmlData->ActiveTestInstances : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiComponentCallStatistics
     */
    public function GetOtapiCallStatistics(){
        $value = isset($this->xmlData->OtapiCallStatistics) ? $this->xmlData->OtapiCallStatistics : false;
        return new OtapiComponentCallStatistics($value);
    }
    /**
     * @return OtapiComponentCallStatistics
     */
    public function GetOtapiAllCallStatistics(){
        $value = isset($this->xmlData->OtapiAllCallStatistics) ? $this->xmlData->OtapiAllCallStatistics : false;
        return new OtapiComponentCallStatistics($value);
    }
    /**
     * @return OtapiComponentCallStatistics
     */
    public function GetTotalLengthTranslatedTexts(){
        $value = isset($this->xmlData->TotalLengthTranslatedTexts) ? $this->xmlData->TotalLengthTranslatedTexts : false;
        return new OtapiComponentCallStatistics($value);
    }
    /**
     * @return OtapiComponentCallStatistics
     */
    public function GetLengthExternalTranslatedTexts(){
        $value = isset($this->xmlData->LengthExternalTranslatedTexts) ? $this->xmlData->LengthExternalTranslatedTexts : false;
        return new OtapiComponentCallStatistics($value);
    }
    /**
     * @return OtapiComponentCallStatistics
     */
    public function GetCachedAdapterCalltatistics(){
        $value = isset($this->xmlData->CachedAdapterCalltatistics) ? $this->xmlData->CachedAdapterCalltatistics : false;
        return new OtapiComponentCallStatistics($value);
    }
    /**
     * @return OtapiComponentCallStatistics
     */
    public function GetAdapterCalltatistics(){
        $value = isset($this->xmlData->AdapterCalltatistics) ? $this->xmlData->AdapterCalltatistics : false;
        return new OtapiComponentCallStatistics($value);
    }
}