<?php

class OtapiArrayOfTariffHistoryElement extends BaseOtapiType{
    /**
     * @return OtapiTariffHistoryElement[]
     */
    public function GetTariffHistoryElement(){
        return isset($this->xmlData->TariffHistoryElement) ? new UnboundedElementsIterator(
                $this->xmlData->TariffHistoryElement,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiTariffHistoryElement'
                )
            ) : array();
    }
}