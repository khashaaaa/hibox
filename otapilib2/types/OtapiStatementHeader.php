<?php

class OtapiStatementHeader extends BaseOtapiType{
    /**
     * @return decimal
     */
    public function GetStartBalance(){
        $value = isset($this->xmlData->StartBalance) ? (string)$this->xmlData->StartBalance : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetDebit(){
        $value = isset($this->xmlData->Debit) ? (string)$this->xmlData->Debit : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetCredit(){
        $value = isset($this->xmlData->Credit) ? (string)$this->xmlData->Credit : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetEndBalance(){
        $value = isset($this->xmlData->EndBalance) ? (string)$this->xmlData->EndBalance : false;
        $propertyType = 'decimal';
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
}