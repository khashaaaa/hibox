<?php

class OtapiSalesPaymentInfo extends BaseOtapiType{
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
    public function GetCustId(){
        $value = isset($this->xmlData->CustId) ? (string)$this->xmlData->CustId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetCustBalanceAvail(){
        $value = isset($this->xmlData->CustBalanceAvail) ? (string)$this->xmlData->CustBalanceAvail : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetSalesAmount(){
        $value = isset($this->xmlData->SalesAmount) ? (string)$this->xmlData->SalesAmount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetSalesPaid(){
        $value = isset($this->xmlData->SalesPaid) ? (string)$this->xmlData->SalesPaid : false;
        $propertyType = 'decimal';
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
}