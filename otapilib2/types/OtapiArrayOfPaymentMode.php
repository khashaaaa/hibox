<?php

class OtapiArrayOfPaymentMode extends BaseOtapiType{
    /**
     * @return OtapiPaymentMode[]
     */
    public function GetPaymentMode(){
        return isset($this->xmlData->PaymentMode) ? new UnboundedElementsIterator(
                $this->xmlData->PaymentMode,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiPaymentMode'
                )
            ) : array();
    }
}