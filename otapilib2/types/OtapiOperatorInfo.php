<?php

class OtapiOperatorInfo extends BaseOtapiType{
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
    public function GetFirstName(){
        $value = isset($this->xmlData->FirstName) ? (string)$this->xmlData->FirstName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetMiddleName(){
        $value = isset($this->xmlData->MiddleName) ? (string)$this->xmlData->MiddleName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLastName(){
        $value = isset($this->xmlData->LastName) ? (string)$this->xmlData->LastName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsSalesOperator(){
        $value = isset($this->xmlData->IsSalesOperator) ? (string)$this->xmlData->IsSalesOperator : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}