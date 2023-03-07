<?php

class OtapiTariffRentalPaymentInfo extends OtapiRentalPaymentInfo{
    /**
     * @return OtapiTurnoverInfo
     */
    public function GetTurnover(){
        $value = isset($this->xmlData->Turnover) ? $this->xmlData->Turnover : false;
        return new OtapiTurnoverInfo($value);
    }
    /**
     * @return long
     */
    public function GetCallCount(){
        $value = isset($this->xmlData->CallCount) ? (string)$this->xmlData->CallCount : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiTariffInfo
     */
    public function GetTariff(){
        $value = isset($this->xmlData->Tariff) ? $this->xmlData->Tariff : false;
        return new OtapiTariffInfo($value);
    }
}