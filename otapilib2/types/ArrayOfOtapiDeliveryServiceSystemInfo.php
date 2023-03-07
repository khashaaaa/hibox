<?php

class ArrayOfOtapiDeliveryServiceSystemInfo extends BaseOtapiType{
    /**
     * @return OtapiDeliveryServiceSystemInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiDeliveryServiceSystemInfo'
                )
            ) : array();
    }
}