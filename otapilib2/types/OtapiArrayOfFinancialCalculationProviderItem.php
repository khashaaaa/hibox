<?php

class OtapiArrayOfFinancialCalculationProviderItem extends BaseOtapiType{
    /**
     * @return OtapiFinancialCalculationProviderItem[]
     */
    public function GetProvider(){
        return isset($this->xmlData->Provider) ? new UnboundedElementsIterator(
                $this->xmlData->Provider,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiFinancialCalculationProviderItem'
                )
            ) : array();
    }
}