<?php

class OtapiArrayOfFieldMetaConstraintDescription extends BaseOtapiType{
    /**
     * @return OtapiFieldMetaConstraintDescription[]
     */
    public function GetDescription(){
        return isset($this->xmlData->Description) ? new UnboundedElementsIterator(
                $this->xmlData->Description,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiFieldMetaConstraintDescription'
                )
            ) : array();
    }
}