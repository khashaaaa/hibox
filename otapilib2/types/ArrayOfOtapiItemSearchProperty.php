<?php

class ArrayOfOtapiItemSearchProperty extends BaseOtapiType{
    /**
     * @return OtapiItemSearchProperty[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiItemSearchProperty'
                )
            ) : array();
    }
}