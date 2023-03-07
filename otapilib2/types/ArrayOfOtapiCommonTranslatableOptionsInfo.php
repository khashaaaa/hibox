<?php

class ArrayOfOtapiCommonTranslatableOptionsInfo extends BaseOtapiType{
    /**
     * @return OtapiCommonTranslatableOptionsInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiCommonTranslatableOptionsInfo'
                )
            ) : array();
    }
}