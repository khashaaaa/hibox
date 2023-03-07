<?php

class OtapiStatementHeaderAdministration extends BaseOtapiType{
    /**
     * @return decimal
     */
    public function GetStartBalanceInternal(){
        $value = isset($this->xmlData->StartBalanceInternal) ? (string)$this->xmlData->StartBalanceInternal : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetDebitInternal(){
        $value = isset($this->xmlData->DebitInternal) ? (string)$this->xmlData->DebitInternal : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetCreditInternal(){
        $value = isset($this->xmlData->CreditInternal) ? (string)$this->xmlData->CreditInternal : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetEndBalanceInternal(){
        $value = isset($this->xmlData->EndBalanceInternal) ? (string)$this->xmlData->EndBalanceInternal : false;
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
     * @return decimal
     */
    public function GetStartBalanceCust(){
        $value = isset($this->xmlData->StartBalanceCust) ? (string)$this->xmlData->StartBalanceCust : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetDebitCust(){
        $value = isset($this->xmlData->DebitCust) ? (string)$this->xmlData->DebitCust : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetCreditCust(){
        $value = isset($this->xmlData->CreditCust) ? (string)$this->xmlData->CreditCust : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetEndBalanceCust(){
        $value = isset($this->xmlData->EndBalanceCust) ? (string)$this->xmlData->EndBalanceCust : false;
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
     * @return int
     */
    public function GetTransCount(){
        $value = isset($this->xmlData->TransCount) ? (string)$this->xmlData->TransCount : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}