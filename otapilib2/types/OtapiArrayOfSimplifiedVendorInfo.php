<?php

class OtapiArrayOfSimplifiedVendorInfo extends BaseOtapiType{
    /**
     * @return OtapiSimplifiedVendorInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiSimplifiedVendorInfo'
                )
            ) : array();
    }
}