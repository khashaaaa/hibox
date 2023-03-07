<?php

class ArrayOfOtapiConfiguredItemPromotion extends BaseOtapiType{
    /**
     * @return OtapiConfiguredItemPromotion[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiConfiguredItemPromotion'
                )
            ) : array();
    }
}