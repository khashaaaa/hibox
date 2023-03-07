<?php

class OtapiArrayOfOrderTotalCostPerCurrency extends BaseOtapiType{
    /**
     * @return OtapiOrderTotalCostPerCurrency[]
     */
    public function GetOrderTotalCostPerCurrency(){
        return isset($this->xmlData->OrderTotalCostPerCurrency) ? new UnboundedElementsIterator(
                $this->xmlData->OrderTotalCostPerCurrency,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiOrderTotalCostPerCurrency'
                )
            ) : array();
    }
}