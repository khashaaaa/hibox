<?php

class OtapiArrayOfContentType extends BaseOtapiType{
    /**
     * @return string[]
     */
    public function GetContentType(){
        return isset($this->xmlData->ContentType) ? new UnboundedElementsIterator(
                $this->xmlData->ContentType,
                array(
                    'type' => 'scalarType',
                    'name' => 'string'
                )
            ) : array();
    }
}