<?php

class OtapiArrayOfItemMarketPriceInfo extends BaseOtapiType{
    /**
     * @return OtapiItemMarketPriceInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiItemMarketPriceInfo'
                )
            ) : array();
    }
}