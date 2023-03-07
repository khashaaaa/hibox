<?php

class OtapiTurnoverInfo extends BaseOtapiType{
    /**
     * @return decimal
     */
    public function GetSumInInternalCurrency(){
        $value = isset($this->xmlData->SumInInternalCurrency) ? (string)$this->xmlData->SumInInternalCurrency : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfTransactionInfoAdministration
     */
    public function GetTransactions(){
        $value = isset($this->xmlData->Transactions) ? $this->xmlData->Transactions : false;
        return new OtapiArrayOfTransactionInfoAdministration($value);
    }
    /**
     * @return OtapiMoney
     */
    public function GetSumMoney(){
        $value = isset($this->xmlData->SumMoney) ? $this->xmlData->SumMoney : false;
        return new OtapiMoney($value);
    }
}