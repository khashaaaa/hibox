<?php

class OtapiArrayOfAdditionalPriceInfo extends BaseOtapiType{
    /**
     * @return OtapiAdditionalPriceInfo[]
     */
    public function GetAdditionalPriceInfo(){
        return isset($this->xmlData->AdditionalPriceInfo) ? new UnboundedElementsIterator(
                $this->xmlData->AdditionalPriceInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiAdditionalPriceInfo'
                )
            ) : array();
    }
}