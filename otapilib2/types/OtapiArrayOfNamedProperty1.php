<?php

class OtapiArrayOfNamedProperty1 extends BaseOtapiType{
    /**
     * @return OtapiNamedProperty[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiNamedProperty'
                )
            ) : array();
    }
}