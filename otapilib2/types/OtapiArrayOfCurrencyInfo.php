<?php

class OtapiArrayOfCurrencyInfo extends BaseOtapiType{
    /**
     * @return OtapiCurrencyInfo[]
     */
    public function GetCurrencyInfo(){
        return isset($this->xmlData->CurrencyInfo) ? new UnboundedElementsIterator(
                $this->xmlData->CurrencyInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiCurrencyInfo'
                )
            ) : array();
    }
}