<?php

class ArrayOfOtapiPromoItemInfo extends BaseOtapiType{
    /**
     * @return OtapiPromoItemInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiPromoItemInfo'
                )
            ) : array();
    }
}