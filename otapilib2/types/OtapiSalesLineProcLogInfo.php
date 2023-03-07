<?php

class OtapiSalesLineProcLogInfo extends BaseOtapiType{
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
    public function GetOperatorId(){
        $value = isset($this->xmlData->OperatorId) ? (string)$this->xmlData->OperatorId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCustId(){
        $value = isset($this->xmlData->CustId) ? (string)$this->xmlData->CustId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetOperatorName(){
        $value = isset($this->xmlData->OperatorName) ? (string)$this->xmlData->OperatorName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCustName(){
        $value = isset($this->xmlData->CustName) ? (string)$this->xmlData->CustName : false;
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
}