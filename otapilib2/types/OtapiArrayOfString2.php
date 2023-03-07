<?php

class OtapiArrayOfString2 extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'scalarType',
                    'name' => 'string'
                )
            ) : array();
    }
}