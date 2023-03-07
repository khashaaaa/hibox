<?php

class OtapiArrayOfRentalPaymentInfo extends BaseOtapiType{
    /**
     * @return OtapiRentalPaymentInfo[]
     */
    public function GetRentalPaymentInfo(){
        return isset($this->xmlData->RentalPaymentInfo) ? new UnboundedElementsIterator(
                $this->xmlData->RentalPaymentInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiRentalPaymentInfo'
                )
            ) : array();
    }
}