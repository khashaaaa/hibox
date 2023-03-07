<?php

class OtapiArrayOfDirectPaymentInfo extends BaseOtapiType{
    /**
     * @return OtapiDirectPaymentInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiDirectPaymentInfo'
                )
            ) : array();
    }
}