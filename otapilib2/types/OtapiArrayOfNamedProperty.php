<?php

class OtapiArrayOfNamedProperty extends BaseOtapiType{
    /**
     * @return OtapiNamedProperty[]
     */
    public function GetNamedProperty(){
        return isset($this->xmlData->NamedProperty) ? new UnboundedElementsIterator(
                $this->xmlData->NamedProperty,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiNamedProperty'
                )
            ) : array();
    }
}