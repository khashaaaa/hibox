<?php

class OtapiFeatureRentalPaymentInfo extends OtapiRentalPaymentInfo{
    /**
     * @return OtapiFeatureTypeInfo
     */
    public function GetFeature(){
        $value = isset($this->xmlData->Feature) ? $this->xmlData->Feature : false;
        return new OtapiFeatureTypeInfo($value);
    }
}