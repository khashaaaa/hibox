<?php

class ArrayOfOtapiCategory extends BaseOtapiType{
    /**
     * @return OtapiCategory[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiCategory'
                )
            ) : array();
    }
}