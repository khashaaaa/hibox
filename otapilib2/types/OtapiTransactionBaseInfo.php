<?php

class OtapiTransactionBaseInfo extends BaseOtapiType{
    /**
     * @return OtapiTransactionTypeInfo
     */
    public function GetTransactionType(){
        $value = isset($this->xmlData->TransactionType) ? $this->xmlData->TransactionType : false;
        return new OtapiTransactionTypeInfo($value);
    }
    /**
     * @return string
     */
    public function GetTransDate(){
        $value = isset($this->xmlData->TransDate) ? (string)$this->xmlData->TransDate : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return dateTime
     */
    public function GetTransactionDateTime(){
        $value = isset($this->xmlData->TransactionDateTime) ? (string)$this->xmlData->TransactionDateTime : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetComment(){
        $value = isset($this->xmlData->Comment) ? (string)$this->xmlData->Comment : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}