<?php

class OtapiOrderLineStatusSummary extends BaseOtapiType{
    /**
     * @return OtapiSalesLineStatusInfo
     */
    public function GetStatus(){
        $value = isset($this->xmlData->Status) ? $this->xmlData->Status : false;
        return new OtapiSalesLineStatusInfo($value);
    }
    /**
     * @return int
     */
    public function GetCount(){
        $value = isset($this->xmlData->Count) ? (string)$this->xmlData->Count : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}