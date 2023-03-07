<?php

class OtapiArrayOfSimplifiedItemProperty extends BaseOtapiType{
    /**
     * @return OtapiSimplifiedItemProperty[]
     */
    public function GetProperty(){
        return isset($this->xmlData->Property) ? new UnboundedElementsIterator(
                $this->xmlData->Property,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiSimplifiedItemProperty'
                )
            ) : array();
    }
}