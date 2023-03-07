<?php

class OtapiCallStatistic extends BaseOtapiType{
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
    public function GetCallLimit(){
        $value = isset($this->xmlData->CallLimit) ? (string)$this->xmlData->CallLimit : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}