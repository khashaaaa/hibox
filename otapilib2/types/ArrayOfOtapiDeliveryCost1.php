<?php

class ArrayOfOtapiDeliveryCost1 extends BaseOtapiType{
    /**
     * @return OtapiDeliveryCost[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiDeliveryCost'
                )
            ) : array();
    }
}