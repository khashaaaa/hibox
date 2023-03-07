<?php

class OtapiArrayOfMarketMerchInfo extends BaseOtapiType{
    /**
     * @return OtapiMarketMerchInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiMarketMerchInfo'
                )
            ) : array();
    }
}