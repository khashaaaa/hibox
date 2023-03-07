<?php

class ArrayOfOtapiWarehouseCategoryInfo extends BaseOtapiType{
    /**
     * @return OtapiWarehouseCategoryInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiWarehouseCategoryInfo'
                )
            ) : array();
    }
}