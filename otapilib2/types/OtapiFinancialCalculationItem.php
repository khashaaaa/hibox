<?php

class OtapiFinancialCalculationItem extends BaseOtapiType{
    /**
     * @return dateTime
     */
    public function GetDate(){
        $value = isset($this->xmlData->Date) ? (string)$this->xmlData->Date : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfFinancialCalculationProviderItem
     */
    public function GetProviders(){
        $value = isset($this->xmlData->Providers) ? $this->xmlData->Providers : false;
        return new OtapiArrayOfFinancialCalculationProviderItem($value);
    }
    /**
     * @return decimal
     */
    public function GetIncomeAmount(){
        $value = isset($this->xmlData->IncomeAmount) ? (string)$this->xmlData->IncomeAmount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetOrdersReservedAmount(){
        $value = isset($this->xmlData->OrdersReservedAmount) ? (string)$this->xmlData->OrdersReservedAmount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetPurchaseAmount(){
        $value = isset($this->xmlData->PurchaseAmount) ? (string)$this->xmlData->PurchaseAmount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetExternalDeliveryAmount(){
        $value = isset($this->xmlData->ExternalDeliveryAmount) ? (string)$this->xmlData->ExternalDeliveryAmount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetEarningsAmount(){
        $value = isset($this->xmlData->EarningsAmount) ? (string)$this->xmlData->EarningsAmount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetEarningsPercent(){
        $value = isset($this->xmlData->EarningsPercent) ? (string)$this->xmlData->EarningsPercent : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}