<?php

class OtapiArrayOfDeliveryMode extends BaseOtapiType{
    /**
     * @return OtapiDeliveryMode[]
     */
    public function GetDeliveryMode(){
        return isset($this->xmlData->DeliveryMode) ? new UnboundedElementsIterator(
                $this->xmlData->DeliveryMode,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiDeliveryMode'
                )
            ) : array();
    }
}