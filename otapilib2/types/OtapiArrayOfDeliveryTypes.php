<?php

class OtapiArrayOfDeliveryTypes extends BaseOtapiType{
    /**
     * @return string[]
     */
    public function GetDeliveryTypes(){
        return isset($this->xmlData->DeliveryTypes) ? new UnboundedElementsIterator(
                $this->xmlData->DeliveryTypes,
                array(
                    'type' => 'scalarType',
                    'name' => 'string'
                )
            ) : array();
    }
}