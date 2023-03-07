<?php

class OtapiArrayOfCurrencyRate extends BaseOtapiType{
    /**
     * @return OtapiCurrencyRate[]
     */
    public function GetCurrencyRate(){
        return isset($this->xmlData->CurrencyRate) ? new UnboundedElementsIterator(
                $this->xmlData->CurrencyRate,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiCurrencyRate'
                )
            ) : array();
    }
}