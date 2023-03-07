<?php

class OtapiMetaListOfDirectPaymentModeSettings extends BaseOtapiType{
    /**
     * @return OtapiDirectPaymentModeSettings[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiDirectPaymentModeSettings'
                )
            ) : array();
    }
}