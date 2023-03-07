<?php

class OtapiAccountAdministrationInfo extends BaseOtapiType{
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
    public function GetBalanceInternal(){
        $value = isset($this->xmlData->BalanceInternal) ? (string)$this->xmlData->BalanceInternal : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetReservedInternal(){
        $value = isset($this->xmlData->ReservedInternal) ? (string)$this->xmlData->ReservedInternal : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetAvailableInternal(){
        $value = isset($this->xmlData->AvailableInternal) ? (string)$this->xmlData->AvailableInternal : false;
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
    public function GetBalanceCust(){
        $value = isset($this->xmlData->BalanceCust) ? (string)$this->xmlData->BalanceCust : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetReservedCust(){
        $value = isset($this->xmlData->ReservedCust) ? (string)$this->xmlData->ReservedCust : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetAvailableCust(){
        $value = isset($this->xmlData->AvailableCust) ? (string)$this->xmlData->AvailableCust : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetPaymWaitInternal(){
        $value = isset($this->xmlData->PaymWaitInternal) ? (string)$this->xmlData->PaymWaitInternal : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetPaymWaitCust(){
        $value = isset($this->xmlData->PaymWaitCust) ? (string)$this->xmlData->PaymWaitCust : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}