<?php

class OtapiArrayOfCurrencyRate1 extends BaseOtapiType{
    /**
     * @return OtapiCurrencyRate[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiCurrencyRate'
                )
            ) : array();
    }
}