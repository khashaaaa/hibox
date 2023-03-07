<?php

class OtapiArrayOfMarketMerchPriceConfig extends BaseOtapiType{
    /**
     * @return OtapiMarketMerchPriceConfig[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiMarketMerchPriceConfig'
                )
            ) : array();
    }
}