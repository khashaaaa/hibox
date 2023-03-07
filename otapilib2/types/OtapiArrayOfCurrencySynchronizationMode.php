<?php

class OtapiArrayOfCurrencySynchronizationMode extends BaseOtapiType{
    /**
     * @return OtapiCurrencySynchronizationMode[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiCurrencySynchronizationMode'
                )
            ) : array();
    }
}