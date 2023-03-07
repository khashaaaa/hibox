<?php

class OtapiArrayOfFeatureRentalPaymentInfo extends BaseOtapiType{
    /**
     * @return OtapiFeatureRentalPaymentInfo[]
     */
    public function GetFeatureRentalPaymentInfo(){
        return isset($this->xmlData->FeatureRentalPaymentInfo) ? new UnboundedElementsIterator(
                $this->xmlData->FeatureRentalPaymentInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiFeatureRentalPaymentInfo'
                )
            ) : array();
    }
}