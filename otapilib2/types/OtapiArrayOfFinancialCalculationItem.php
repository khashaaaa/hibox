<?php

class OtapiArrayOfFinancialCalculationItem extends BaseOtapiType{
    /**
     * @return OtapiFinancialCalculationItem[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiFinancialCalculationItem'
                )
            ) : array();
    }
}