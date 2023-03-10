<?php

class OtapiRentalPaymentInfo extends BaseOtapiType{
    /**
     * @return dateTime
     */
    public function GetDateFrom(){
        $value = isset($this->xmlData->DateFrom) ? (string)$this->xmlData->DateFrom : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return dateTime
     */
    public function GetDateTo(){
        $value = isset($this->xmlData->DateTo) ? (string)$this->xmlData->DateTo : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiBasePrice
     */
    public function GetSum(){
        $value = isset($this->xmlData->Sum) ? $this->xmlData->Sum : false;
        return new OtapiBasePrice($value);
    }
}