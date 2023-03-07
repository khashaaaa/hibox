<?php

class OtapiArrayOfExternalDeliveryRate extends BaseOtapiType{
    /**
     * @return OtapiExternalDeliveryRate[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiExternalDeliveryRate'
                )
            ) : array();
    }
}