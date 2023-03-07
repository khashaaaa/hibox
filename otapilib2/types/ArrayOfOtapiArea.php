<?php

class ArrayOfOtapiArea extends BaseOtapiType{
    /**
     * @return OtapiArea[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiArea'
                )
            ) : array();
    }
}