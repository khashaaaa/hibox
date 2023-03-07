<?php

class ArrayOfOtapiItemPromotion1 extends BaseOtapiType{
    /**
     * @return OtapiItemPromotion[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiItemPromotion'
                )
            ) : array();
    }
}