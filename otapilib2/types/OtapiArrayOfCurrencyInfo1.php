<?php

class OtapiArrayOfCurrencyInfo1 extends BaseOtapiType{
    /**
     * @return OtapiCurrencyInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiCurrencyInfo'
                )
            ) : array();
    }
}