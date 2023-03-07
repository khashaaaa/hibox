<?php

class OtapiArchiveRecord extends BaseOtapiType{
    /**
     * @return long
     */
    public function GetCount(){
        $value = isset($this->xmlData->Count) ? (string)$this->xmlData->Count : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return dateTime
     */
    public function GetTimeMeasure(){
        $value = isset($this->xmlData->TimeMeasure) ? (string)$this->xmlData->TimeMeasure : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}