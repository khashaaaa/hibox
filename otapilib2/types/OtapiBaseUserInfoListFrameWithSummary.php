<?php

class OtapiBaseUserInfoListFrameWithSummary extends OtapiDataSubListOfBaseUserInfo{
    /**
     * @return decimal
     */
    public function GetTotalReceived(){
        $value = isset($this->xmlData->TotalReceived) ? (string)$this->xmlData->TotalReceived : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetTotalReserved(){
        $value = isset($this->xmlData->TotalReserved) ? (string)$this->xmlData->TotalReserved : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetTotalAvailable(){
        $value = isset($this->xmlData->TotalAvailable) ? (string)$this->xmlData->TotalAvailable : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetTotalWaitingForPayment(){
        $value = isset($this->xmlData->TotalWaitingForPayment) ? (string)$this->xmlData->TotalWaitingForPayment : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}