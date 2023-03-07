<?php

class ArrayOfOtapiPaymentInfo extends BaseOtapiType{
    /**
     * @return OtapiPaymentInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiPaymentInfo'
                )
            ) : array();
    }
}