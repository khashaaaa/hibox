<?php
OTBase::import('system.lib.service.for_generator_2_0.BaseElement');
OTBase::import('system.lib.service.for_generator_2_0.complexType.ArrayOfOtapiCategory');

class DataListOfOtapiCategory extends BaseElement {
    public function GetContent(){
        return isset($this->xmlData->Content) ? new ArrayOfOtapiCategory($this->xmlData->Content) : false;
    }
}