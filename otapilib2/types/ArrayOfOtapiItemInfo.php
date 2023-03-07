<?php

class ArrayOfOtapiItemInfo extends BaseOtapiType{
    /**
     * @return OtapiItemInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiItemInfo'
                )
            ) : array();
    }
}