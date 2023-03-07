<?php

class OtapiVendorScores extends BaseOtapiType{
    /**
     * @return decimal
     */
    public function GetDeliveryScore(){
        $value = isset($this->xmlData->DeliveryScore) ? (string)$this->xmlData->DeliveryScore : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetItemScore(){
        $value = isset($this->xmlData->ItemScore) ? (string)$this->xmlData->ItemScore : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetServiceScore(){
        $value = isset($this->xmlData->ServiceScore) ? (string)$this->xmlData->ServiceScore : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}