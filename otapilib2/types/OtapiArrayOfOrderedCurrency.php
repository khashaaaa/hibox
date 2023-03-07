<?php

class OtapiArrayOfOrderedCurrency extends BaseOtapiType{
    /**
     * @return OtapiOrderedCurrency[]
     */
    public function GetOrderedCurrency(){
        return isset($this->xmlData->OrderedCurrency) ? new UnboundedElementsIterator(
                $this->xmlData->OrderedCurrency,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiOrderedCurrency'
                )
            ) : array();
    }
}