<?php
OTBase::import('system.lib.service.for_generator_2_0.BaseElement');
OTBase::import('system.lib.service.for_generator_2_0.UnboundedElementsIterator');

class ArrayOfEditableOtapiCategory extends BaseElement {
    /**
     * @return UnboundedElementsIterator|EditableOtapiCategory[]
     */
    public function GetItems(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
            $this->xmlData->Item,
            array(
                'type' => 'complexType',
                'name' => 'EditableOtapiCategory'
            )
        ) : array();
    }
}