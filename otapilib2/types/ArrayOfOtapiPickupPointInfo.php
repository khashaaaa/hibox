<?php

class ArrayOfOtapiPickupPointInfo extends BaseOtapiType{
    /**
     * @return OtapiPickupPointInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiPickupPointInfo'
                )
            ) : array();
    }
}