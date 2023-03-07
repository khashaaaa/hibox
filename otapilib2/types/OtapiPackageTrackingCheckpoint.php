<?php

class OtapiPackageTrackingCheckpoint extends BaseOtapiType{
    /**
     * @return dateTime
     */
    public function GetTime(){
        $value = isset($this->xmlData->Time) ? (string)$this->xmlData->Time : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetStatus(){
        $value = isset($this->xmlData->Status) ? (string)$this->xmlData->Status : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLocation(){
        $value = isset($this->xmlData->Location) ? (string)$this->xmlData->Location : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}