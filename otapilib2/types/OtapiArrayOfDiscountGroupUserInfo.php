<?php

class OtapiArrayOfDiscountGroupUserInfo extends BaseOtapiType{
    /**
     * @return OtapiDiscountGroupUserInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiDiscountGroupUserInfo'
                )
            ) : array();
    }
}