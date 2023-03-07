<?php

class OtapiArrayOfFeaturedValue extends BaseOtapiType{
    /**
     * @return OtapiFeaturedValue[]
     */
    public function GetValue(){
        return isset($this->xmlData->Value) ? new UnboundedElementsIterator(
                $this->xmlData->Value,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiFeaturedValue'
                )
            ) : array();
    }
}