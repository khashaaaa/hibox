<?php

class OtapiDiscountIdentificationParametrInfo extends BaseOtapiType{
    /**
     * @return OtapiDataListOfItemProviderType
     */
    public function GetProviders(){
        $value = isset($this->xmlData->Providers) ? $this->xmlData->Providers : false;
        return new OtapiDataListOfItemProviderType($value);
    }
    /**
     * @return decimal
     */
    public function GetPurchaseVolume(){
        $value = isset($this->xmlData->PurchaseVolume) ? (string)$this->xmlData->PurchaseVolume : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}