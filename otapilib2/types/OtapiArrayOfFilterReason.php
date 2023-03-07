<?php

class OtapiArrayOfFilterReason extends BaseOtapiType{
    /**
     * @return string[]
     */
    public function GetReason(){
        return isset($this->xmlData->Reason) ? new UnboundedElementsIterator(
                $this->xmlData->Reason,
                array(
                    'type' => 'scalarType',
                    'name' => 'string'
                )
            ) : array();
    }
}