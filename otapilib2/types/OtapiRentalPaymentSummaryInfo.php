<?php

class OtapiRentalPaymentSummaryInfo extends BaseOtapiType{
    /**
     * @return OtapiTariffRentalPaymentInfo
     */
    public function GetTotalRent(){
        $value = isset($this->xmlData->TotalRent) ? $this->xmlData->TotalRent : false;
        return new OtapiTariffRentalPaymentInfo($value);
    }
    /**
     * @return OtapiArrayOfTariffRentalPaymentInfo
     */
    public function GetRentPerPeriods(){
        $value = isset($this->xmlData->RentPerPeriods) ? $this->xmlData->RentPerPeriods : false;
        return new OtapiArrayOfTariffRentalPaymentInfo($value);
    }
    /**
     * @return OtapiArrayOfFeatureRentalPaymentInfo
     */
    public function GetFeatureRentPerPeriods(){
        $value = isset($this->xmlData->FeatureRentPerPeriods) ? $this->xmlData->FeatureRentPerPeriods : false;
        return new OtapiArrayOfFeatureRentalPaymentInfo($value);
    }
}