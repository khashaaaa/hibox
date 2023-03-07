<?php

class OtapiArrayOfString extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetFeature(){
        return isset($this->xmlData->Feature) ? new UnboundedElementsIterator(
                $this->xmlData->Feature,
                array(
                    'type' => 'scalarType',
                    'name' => 'string'
                )
            ) : array();
    }
}