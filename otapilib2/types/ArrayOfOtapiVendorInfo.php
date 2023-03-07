<?php

class ArrayOfOtapiVendorInfo extends BaseOtapiType{
    /**
     * @return OtapiVendorInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiVendorInfo'
                )
            ) : array();
    }
}