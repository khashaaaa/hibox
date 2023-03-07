<?php

class OtapiArrayOfExternalDeliveryType extends BaseOtapiType{
    /**
     * @return OtapiExternalDeliveryType[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiExternalDeliveryType'
                )
            ) : array();
    }
}