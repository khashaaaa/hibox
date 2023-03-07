<?php

class OtapiFinancialReportBalanceInfo extends BaseOtapiType{
    /**
     * @return decimal
     */
    public function GetPositiveAmount(){
        $value = isset($this->xmlData->PositiveAmount) ? (string)$this->xmlData->PositiveAmount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetNegativeAmount(){
        $value = isset($this->xmlData->NegativeAmount) ? (string)$this->xmlData->NegativeAmount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetTotalAmount(){
        $value = isset($this->xmlData->TotalAmount) ? (string)$this->xmlData->TotalAmount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}