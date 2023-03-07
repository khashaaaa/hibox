<?php

class ArrayOfOtapiValuedConfigurator extends BaseOtapiType{
    /**
     * @return OtapiValuedConfigurator[]
     */
    public function GetValuedConfigurator(){
        return isset($this->xmlData->ValuedConfigurator) ? new UnboundedElementsIterator(
                $this->xmlData->ValuedConfigurator,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiValuedConfigurator'
                )
            ) : array();
    }
}