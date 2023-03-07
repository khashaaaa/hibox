<?php

class OtapiArrayOfSimplifiedQuantityRange extends BaseOtapiType{
    /**
     * @return OtapiSimplifiedQuantityRange[]
     */
    public function GetRange(){
        return isset($this->xmlData->Range) ? new UnboundedElementsIterator(
                $this->xmlData->Range,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiSimplifiedQuantityRange'
                )
            ) : array();
    }
}