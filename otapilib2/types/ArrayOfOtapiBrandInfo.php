<?php

class ArrayOfOtapiBrandInfo extends BaseOtapiType{
    /**
     * @return OtapiBrandInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiBrandInfo'
                )
            ) : array();
    }
}