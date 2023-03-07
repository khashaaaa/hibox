<?php

class ArrayOfOtapiItemAttribute extends BaseOtapiType{
    /**
     * @return OtapiItemAttribute[]
     */
    public function GetItemAttribute(){
        return isset($this->xmlData->ItemAttribute) ? new UnboundedElementsIterator(
                $this->xmlData->ItemAttribute,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiItemAttribute'
                )
            ) : array();
    }
}