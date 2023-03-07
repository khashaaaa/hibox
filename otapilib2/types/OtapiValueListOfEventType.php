<?php

class OtapiValueListOfEventType extends BaseOtapiType{
    /**
     * @return string[]
     */
    public function GetValue(){
        return isset($this->xmlData->Value) ? new UnboundedElementsIterator(
                $this->xmlData->Value,
                array(
                    'type' => 'scalarType',
                    'name' => 'string'
                )
            ) : array();
    }
}