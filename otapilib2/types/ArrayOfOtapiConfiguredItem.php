<?php

class ArrayOfOtapiConfiguredItem extends BaseOtapiType{
    /**
     * @return OtapiConfiguredItem[]
     */
    public function GetOtapiConfiguredItem(){
        return isset($this->xmlData->OtapiConfiguredItem) ? new UnboundedElementsIterator(
                $this->xmlData->OtapiConfiguredItem,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiConfiguredItem'
                )
            ) : array();
    }
}