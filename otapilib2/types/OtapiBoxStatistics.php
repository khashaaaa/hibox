<?php

class OtapiBoxStatistics extends BaseOtapiType{
    /**
     * @return dateTime
     */
    public function GetDate(){
        $value = isset($this->xmlData->Date) ? (string)$this->xmlData->Date : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return double
     */
    public function GetBoxFullTime(){
        $value = isset($this->xmlData->BoxFullTime) ? (string)$this->xmlData->BoxFullTime : false;
        $propertyType = 'double';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return double
     */
    public function GetBoxOtherTime(){
        $value = isset($this->xmlData->BoxOtherTime) ? (string)$this->xmlData->BoxOtherTime : false;
        $propertyType = 'double';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return double
     */
    public function GetBoxWorkTime(){
        $value = isset($this->xmlData->BoxWorkTime) ? (string)$this->xmlData->BoxWorkTime : false;
        $propertyType = 'double';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return double
     */
    public function GetApiNetworkTime(){
        $value = isset($this->xmlData->ApiNetworkTime) ? (string)$this->xmlData->ApiNetworkTime : false;
        $propertyType = 'double';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return double
     */
    public function GetApiWorkTime(){
        $value = isset($this->xmlData->ApiWorkTime) ? (string)$this->xmlData->ApiWorkTime : false;
        $propertyType = 'double';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return double
     */
    public function GetApiNetworkTimePerCall(){
        $value = isset($this->xmlData->ApiNetworkTimePerCall) ? (string)$this->xmlData->ApiNetworkTimePerCall : false;
        $propertyType = 'double';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}