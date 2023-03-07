<?php

class OtapiSalesProcLogInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetLogDate(){
        $value = isset($this->xmlData->LogDate) ? (string)$this->xmlData->LogDate : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLogTime(){
        $value = isset($this->xmlData->LogTime) ? (string)$this->xmlData->LogTime : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetFieldName(){
        $value = isset($this->xmlData->FieldName) ? (string)$this->xmlData->FieldName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPrevValue(){
        $value = isset($this->xmlData->PrevValue) ? (string)$this->xmlData->PrevValue : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetNewValue(){
        $value = isset($this->xmlData->NewValue) ? (string)$this->xmlData->NewValue : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiSalesStatusInfo
     */
    public function GetOldStatus(){
        $value = isset($this->xmlData->OldStatus) ? $this->xmlData->OldStatus : false;
        return new OtapiSalesStatusInfo($value);
    }
    /**
     * @return OtapiSalesStatusInfo
     */
    public function GetNewStatus(){
        $value = isset($this->xmlData->NewStatus) ? $this->xmlData->NewStatus : false;
        return new OtapiSalesStatusInfo($value);
    }
}