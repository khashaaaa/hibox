<?php

class OtapiArrayOfOrderLineStatusSummary extends BaseOtapiType{
    /**
     * @return OtapiOrderLineStatusSummary[]
     */
    public function GetOrderLineStatusSummary(){
        return isset($this->xmlData->OrderLineStatusSummary) ? new UnboundedElementsIterator(
                $this->xmlData->OrderLineStatusSummary,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiOrderLineStatusSummary'
                )
            ) : array();
    }
}