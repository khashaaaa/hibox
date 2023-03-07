<?php

class ArrayOfOtapiWarehouseItemInfo extends BaseOtapiType{
    /**
     * @return OtapiWarehouseItemInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiWarehouseItemInfo'
                )
            ) : array();
    }
}