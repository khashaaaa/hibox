<?php

class OtapiArrayOfInt extends BaseOtapiType{
    /**
     * @return int
     */
    public function GetInt(){
        return isset($this->xmlData->int) ? new UnboundedElementsIterator(
                $this->xmlData->int,
                array(
                    'type' => 'scalarType',
                    'name' => 'int'
                )
            ) : array();
    }
}