<?php

class ArrayOfOtapiSearchBrandInfo extends BaseOtapiType{
    /**
     * @return OtapiSearchBrandInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiSearchBrandInfo'
                )
            ) : array();
    }
}