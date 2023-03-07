<?php

class ArrayOfOtapiDeliveryCost extends BaseOtapiType{
    /**
     * @return OtapiDeliveryCost[]
     */
    public function GetOtapiDeliveryCost(){
        return isset($this->xmlData->OtapiDeliveryCost) ? new UnboundedElementsIterator(
                $this->xmlData->OtapiDeliveryCost,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiDeliveryCost'
                )
            ) : array();
    }
}