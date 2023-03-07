<?php
OTBase::import('system.lib.service.for_generator_2_0.BaseElement');
OTBase::import('system.lib.service.for_generator_2_0.complexType.ArrayOfEditableOtapiCategory');

class DataListOfEditableOtapiCategory extends BaseElement {
    /**
     * @return ArrayOfEditableOtapiCategory|bool
     */
    public function GetContent(){
        return isset($this->xmlData->Content) ? new ArrayOfEditableOtapiCategory($this->xmlData->Content) : false;
    }
}