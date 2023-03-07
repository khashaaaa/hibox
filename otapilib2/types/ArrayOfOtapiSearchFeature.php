<?php

class ArrayOfOtapiSearchFeature extends BaseOtapiType{
    /**
     * @return OtapiSearchFeature[]
     */
    public function GetFeature(){
        return isset($this->xmlData->Feature) ? new UnboundedElementsIterator(
                $this->xmlData->Feature,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiSearchFeature'
                )
            ) : array();
    }
}