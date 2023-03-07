<?php

class ArrayOfOtapiOrderHistoryItem extends BaseOtapiType{
    /**
     * @return OtapiOrderHistoryItem[]
     */
    public function GetOrderHistoryItem(){
        return isset($this->xmlData->OrderHistoryItem) ? new UnboundedElementsIterator(
                $this->xmlData->OrderHistoryItem,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiOrderHistoryItem'
                )
            ) : array();
    }
}