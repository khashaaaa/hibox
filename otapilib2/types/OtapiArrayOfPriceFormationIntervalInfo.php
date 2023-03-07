<?php

class OtapiArrayOfPriceFormationIntervalInfo extends BaseOtapiType{
    /**
     * @return OtapiPriceFormationIntervalInfo[]
     */
    public function GetPriceFormationIntervalInfo(){
        return isset($this->xmlData->PriceFormationIntervalInfo) ? new UnboundedElementsIterator(
                $this->xmlData->PriceFormationIntervalInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiPriceFormationIntervalInfo'
                )
            ) : array();
    }
}