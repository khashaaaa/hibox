<?php

class OtapiArrayOfSimplifiedItemInfo extends BaseOtapiType{
    /**
     * @return OtapiSimplifiedItemInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiSimplifiedItemInfo'
                )
            ) : array();
    }
}