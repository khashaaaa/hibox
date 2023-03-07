<?php

class OtapiMetaListOfSmsServiceModeSettings extends BaseOtapiType{
    /**
     * @return OtapiSmsServiceModeSettings[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiSmsServiceModeSettings'
                )
            ) : array();
    }
}