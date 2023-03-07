<?php

class OtapiArrayOfBillInfo extends BaseOtapiType{
    /**
     * @return OtapiBillInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiBillInfo'
                )
            ) : array();
    }
}