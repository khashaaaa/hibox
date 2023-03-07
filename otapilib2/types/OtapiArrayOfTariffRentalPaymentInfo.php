<?php

class OtapiArrayOfTariffRentalPaymentInfo extends BaseOtapiType{
    /**
     * @return OtapiTariffRentalPaymentInfo[]
     */
    public function GetRentalPaymentInfo(){
        return isset($this->xmlData->RentalPaymentInfo) ? new UnboundedElementsIterator(
                $this->xmlData->RentalPaymentInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiTariffRentalPaymentInfo'
                )
            ) : array();
    }
}