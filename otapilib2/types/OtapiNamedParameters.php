<?php

class OtapiNamedParameters extends BaseOtapiType{
    /**
     * @return OtapiNamedParameter[]
     */
    public function GetParameter(){
        return isset($this->xmlData->Parameter) ? new UnboundedElementsIterator(
                $this->xmlData->Parameter,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiNamedParameter'
                )
            ) : array();
    }
}