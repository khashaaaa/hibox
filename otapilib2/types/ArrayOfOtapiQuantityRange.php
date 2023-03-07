<?php

class ArrayOfOtapiQuantityRange extends BaseOtapiType{
    /**
     * @return OtapiQuantityRange[]
     */
    public function GetRange(){
        return isset($this->xmlData->Range) ? new UnboundedElementsIterator(
            $this->xmlData->Range,
            array(
                'type' => 'complexType',
                'name' => 'OtapiQuantityRange'
            )
        ) : array();
    }
}