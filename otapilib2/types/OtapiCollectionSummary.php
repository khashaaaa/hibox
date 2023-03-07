<?php

class OtapiCollectionSummary extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetProviderType(){
        $value = isset($this->xmlData->ProviderType) ? (string)$this->xmlData->ProviderType : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiBasePrice
     */
    public function GetTotalCost(){
        $value = isset($this->xmlData->TotalCost) ? $this->xmlData->TotalCost : false;
        return new OtapiBasePrice($value);
    }
    /**
     * @return OtapiAdditionalPriceCollectionInfo
     */
    public function GetAdditionalPriceInfoList(){
        $value = isset($this->xmlData->AdditionalPriceInfoList) ? $this->xmlData->AdditionalPriceInfoList : false;
        return new OtapiAdditionalPriceCollectionInfo($value);
    }
}