<?php

class OtapiAccountInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? (string)$this->xmlData->Id : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetNum(){
        $value = isset($this->xmlData->Num) ? (string)$this->xmlData->Num : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCurrencyCode(){
        $value = isset($this->xmlData->CurrencyCode) ? (string)$this->xmlData->CurrencyCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCurrencySign(){
        $value = isset($this->xmlData->CurrencySign) ? (string)$this->xmlData->CurrencySign : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetBalance(){
        $value = isset($this->xmlData->Balance) ? (string)$this->xmlData->Balance : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetReservedAmount(){
        $value = isset($this->xmlData->ReservedAmount) ? (string)$this->xmlData->ReservedAmount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetAvailableAmount(){
        $value = isset($this->xmlData->AvailableAmount) ? (string)$this->xmlData->AvailableAmount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetPaymWaitAmount(){
        $value = isset($this->xmlData->PaymWaitAmount) ? (string)$this->xmlData->PaymWaitAmount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}