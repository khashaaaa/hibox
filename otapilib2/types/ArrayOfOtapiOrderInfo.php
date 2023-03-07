<?php

class ArrayOfOtapiOrderInfo extends BaseOtapiType{
    /**
     * @return OtapiOrderInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiOrderInfo'
                )
            ) : array();
    }
}