<?php

class OtapiFinancialCalculationProviderItem extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetProviderType(){
        $value = isset($this->xmlData->ProviderType) ? (string)$this->xmlData->ProviderType : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetProviderCurrencyCode(){
        $value = isset($this->xmlData->ProviderCurrencyCode) ? (string)$this->xmlData->ProviderCurrencyCode : false;
        $propertyType = 'string';
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
    public function GetPurchaseProviderAmount(){
        $value = isset($this->xmlData->PurchaseProviderAmount) ? (string)$this->xmlData->PurchaseProviderAmount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetExchangeRate(){
        $value = isset($this->xmlData->ExchangeRate) ? (string)$this->xmlData->ExchangeRate : false;
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