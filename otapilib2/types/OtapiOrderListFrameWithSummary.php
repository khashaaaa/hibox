<?php

class OtapiOrderListFrameWithSummary extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiOrderInfo1
     */
    public function GetSalesOrdersList(){
        $value = isset($this->xmlData->SalesOrdersList) ? $this->xmlData->SalesOrdersList : false;
        return new ArrayOfOtapiOrderInfo1($value);
    }
    /**
     * @return int
     */
    public function GetTotalCount(){
        $value = isset($this->xmlData->TotalCount) ? (string)$this->xmlData->TotalCount : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetTotalCostInInternalCurrency(){
        $value = isset($this->xmlData->TotalCostInInternalCurrency) ? (string)$this->xmlData->TotalCostInInternalCurrency : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetTotalProfitInInternalCurrency(){
        $value = isset($this->xmlData->TotalProfitInInternalCurrency) ? (string)$this->xmlData->TotalProfitInInternalCurrency : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}