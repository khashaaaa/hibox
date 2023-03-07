<?php

class ArrayOfOtapiTransactionInfo extends BaseOtapiType{
    /**
     * @return OtapiTransactionInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiTransactionInfo'
                )
            ) : array();
    }
}