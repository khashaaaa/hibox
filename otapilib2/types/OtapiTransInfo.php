<?php

class OtapiTransInfo extends OtapiTransactionBaseInfo{
    /**
     * @return decimal
     */
    public function GetAmount(){
        $value = isset($this->xmlData->Amount) ? (string)$this->xmlData->Amount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetSalesId(){
        $value = isset($this->xmlData->SalesId) ? (string)$this->xmlData->SalesId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDirection(){
        $value = isset($this->xmlData->Direction) ? (string)$this->xmlData->Direction : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}