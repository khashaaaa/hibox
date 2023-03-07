<?php

class OtapiArrayOfPricePercent extends BaseOtapiType{
    /**
     * @return OtapiPricePercent[]
     */
    public function GetPercent(){
        return isset($this->xmlData->Percent) ? new UnboundedElementsIterator(
                $this->xmlData->Percent,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiPricePercent'
                )
            ) : array();
    }
}