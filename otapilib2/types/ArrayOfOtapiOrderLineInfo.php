<?php

class ArrayOfOtapiOrderLineInfo extends BaseOtapiType{
    /**
     * @return OtapiOrderLineInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiOrderLineInfo'
                )
            ) : array();
    }
}