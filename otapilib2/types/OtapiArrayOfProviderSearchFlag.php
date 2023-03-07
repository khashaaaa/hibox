<?php

class OtapiArrayOfProviderSearchFlag extends BaseOtapiType{
    /**
     * @return string[]
     */
    public function GetFlag(){
        return isset($this->xmlData->Flag) ? new UnboundedElementsIterator(
                $this->xmlData->Flag,
                array(
                    'type' => 'scalarType',
                    'name' => 'string'
                )
            ) : array();
    }
}