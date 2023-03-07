<?php

class OtapiArrayOfSimplifiedValueWithId extends BaseOtapiType{
    /**
     * @return OtapiSimplifiedValueWithId[]
     */
    public function GetFeature(){
        return isset($this->xmlData->Feature) ? new UnboundedElementsIterator(
                $this->xmlData->Feature,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiSimplifiedValueWithId'
                )
            ) : array();
    }
}