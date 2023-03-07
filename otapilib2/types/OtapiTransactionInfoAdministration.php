<?php

class OtapiTransactionInfoAdministration extends OtapiTransactionBaseInfo{
    /**
     * @return string
     */
    public function GetTransId(){
        $value = isset($this->xmlData->TransId) ? (string)$this->xmlData->TransId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetAmountCust(){
        $value = isset($this->xmlData->AmountCust) ? (string)$this->xmlData->AmountCust : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCurrencyCodeCust(){
        $value = isset($this->xmlData->CurrencyCodeCust) ? (string)$this->xmlData->CurrencyCodeCust : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCurrencySignCust(){
        $value = isset($this->xmlData->CurrencySignCust) ? (string)$this->xmlData->CurrencySignCust : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetAmountInternal(){
        $value = isset($this->xmlData->AmountInternal) ? (string)$this->xmlData->AmountInternal : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCurrencyCodeInternal(){
        $value = isset($this->xmlData->CurrencyCodeInternal) ? (string)$this->xmlData->CurrencyCodeInternal : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCurrencySignInternal(){
        $value = isset($this->xmlData->CurrencySignInternal) ? (string)$this->xmlData->CurrencySignInternal : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetUserId(){
        $value = isset($this->xmlData->UserId) ? (string)$this->xmlData->UserId : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetUserLogin(){
        $value = isset($this->xmlData->UserLogin) ? (string)$this->xmlData->UserLogin : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}