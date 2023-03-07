<?php

class OtapiArrayOfDiscountGroupInfo extends BaseOtapiType{
    /**
     * @return OtapiDiscountGroupInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiDiscountGroupInfo'
                )
            ) : array();
    }
}