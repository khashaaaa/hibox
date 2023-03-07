<?php

class OtapiArrayOfString6 extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetParameter(){
        return isset($this->xmlData->Parameter) ? new UnboundedElementsIterator(
                $this->xmlData->Parameter,
                array(
                    'type' => 'scalarType',
                    'name' => 'string'
                )
            ) : array();
    }
}