<?php

class ArrayOfOtapiValuedConfigurator1 extends BaseOtapiType{
    /**
     * @return OtapiValuedConfigurator[]
     */
    public function GetConfigurator(){
        return isset($this->xmlData->Configurator) ? new UnboundedElementsIterator(
                $this->xmlData->Configurator,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiValuedConfigurator'
                )
            ) : array();
    }
}