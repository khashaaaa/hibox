<?php

class ArrayOfOtapiSearchCategoryInfo extends BaseOtapiType{
    /**
     * @return OtapiSearchCategoryInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiSearchCategoryInfo'
                )
            ) : array();
    }
}