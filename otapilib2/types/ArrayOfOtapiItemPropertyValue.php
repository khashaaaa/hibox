<?php

class ArrayOfOtapiItemPropertyValue extends BaseOtapiType{
    /**
     * @return OtapiItemPropertyValue[]
     */
    public function GetPropertyValue(){
        return isset($this->xmlData->PropertyValue) ? new UnboundedElementsIterator(
                $this->xmlData->PropertyValue,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiItemPropertyValue'
                )
            ) : array();
    }
}