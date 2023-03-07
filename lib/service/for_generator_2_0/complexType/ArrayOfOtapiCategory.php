<?php
OTBase::import('system.lib.service.for_generator_2_0.BaseElement');

class ArrayOfOtapiCategory extends BaseElement {
    public function GetItems(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
            $this->xmlData->Item,
            array(
                'type' => 'complexType',
                'name' => 'OtapiCategory'
            )
        ) : array();
    }
}