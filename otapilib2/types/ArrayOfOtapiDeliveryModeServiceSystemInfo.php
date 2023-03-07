<?php

class ArrayOfOtapiDeliveryModeServiceSystemInfo extends BaseOtapiType{
    /**
     * @return OtapiDeliveryModeServiceSystemInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiDeliveryModeServiceSystemInfo'
                )
            ) : array();
    }
}