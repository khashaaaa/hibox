<?php

class OtapiArrayOfOperationTypeInfo extends BaseOtapiType{
    /**
     * @return OtapiOperationTypeInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiOperationTypeInfo'
                )
            ) : array();
    }
}