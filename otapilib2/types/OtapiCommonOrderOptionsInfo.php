<?php

class OtapiCommonOrderOptionsInfo extends BaseOtapiType{
    /**
     * @return decimal
     */
    public function GetMinOrderCost(){
        $value = isset($this->xmlData->MinOrderCost) ? (string)$this->xmlData->MinOrderCost : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiInstanceListOfMoney
     */
    public function GetConvertedMinOrderCost(){
        $value = isset($this->xmlData->ConvertedMinOrderCost) ? $this->xmlData->ConvertedMinOrderCost : false;
        return new OtapiInstanceListOfMoney($value);
    }
}