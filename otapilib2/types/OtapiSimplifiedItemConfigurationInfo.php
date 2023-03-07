<?php

class OtapiSimplifiedItemConfigurationInfo extends BaseOtapiType{
    /**
     * @return OtapiSimplifiedValueWithIdOfBoolean
     */
    public function GetAvailability(){
        $value = isset($this->xmlData->Availability) ? $this->xmlData->Availability : false;
        return new OtapiSimplifiedValueWithIdOfBoolean($value);
    }
    /**
     * @return OtapiSimplifiedItemConfigurationPropertyList
     */
    public function GetProperties(){
        $value = isset($this->xmlData->Properties) ? $this->xmlData->Properties : false;
        return new OtapiSimplifiedItemConfigurationPropertyList($value);
    }
    /**
     * @return OtapiSimplifiedItemConfigurationPriceInfo
     */
    public function GetCurrent(){
        $value = isset($this->xmlData->Current) ? $this->xmlData->Current : false;
        return new OtapiSimplifiedItemConfigurationPriceInfo($value);
    }
    /**
     * @return OtapiArrayOfSimplifiedItemConfigurationMultiInputInfo
     */
    public function GetMultiInputConfigurations(){
        $value = isset($this->xmlData->MultiInputConfigurations) ? $this->xmlData->MultiInputConfigurations : false;
        return new OtapiArrayOfSimplifiedItemConfigurationMultiInputInfo($value);
    }
    /**
     * @return OtapiArrayOfSimplifiedQuantityRange
     */
    public function GetQuantityRanges(){
        $value = isset($this->xmlData->QuantityRanges) ? $this->xmlData->QuantityRanges : false;
        return new OtapiArrayOfSimplifiedQuantityRange($value);
    }
    /**
     * @return decimal
     */
    public function GetTotalCost(){
        $value = isset($this->xmlData->TotalCost) ? (string)$this->xmlData->TotalCost : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}