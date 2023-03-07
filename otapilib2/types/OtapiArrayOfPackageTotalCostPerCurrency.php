<?php

class OtapiArrayOfPackageTotalCostPerCurrency extends BaseOtapiType{
    /**
     * @return OtapiPackageTotalCostPerCurrency[]
     */
    public function GetPackageTotalCostPerCurrency(){
        return isset($this->xmlData->PackageTotalCostPerCurrency) ? new UnboundedElementsIterator(
                $this->xmlData->PackageTotalCostPerCurrency,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiPackageTotalCostPerCurrency'
                )
            ) : array();
    }
}