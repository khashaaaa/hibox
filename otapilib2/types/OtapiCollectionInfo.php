<?php

class OtapiCollectionInfo extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiElementInfo
     */
    public function GetElements(){
        $value = isset($this->xmlData->Elements) ? $this->xmlData->Elements : false;
        return new ArrayOfOtapiElementInfo($value);
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
    /**
     * @return OtapiArrayOfCollectionSummary
     */
    public function GetCollectionSummaries(){
        $value = isset($this->xmlData->CollectionSummaries) ? $this->xmlData->CollectionSummaries : false;
        return new OtapiArrayOfCollectionSummary($value);
    }
}