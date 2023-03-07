<?php

class OtapiArrayOfString1 extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetString(){
        return isset($this->xmlData->string) ? new UnboundedElementsIterator(
                $this->xmlData->string,
                array(
                    'type' => 'scalarType',
                    'name' => 'string'
                )
            ) : array();
    }
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