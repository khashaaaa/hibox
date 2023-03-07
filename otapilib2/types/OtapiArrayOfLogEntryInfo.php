<?php

class OtapiArrayOfLogEntryInfo extends BaseOtapiType{
    /**
     * @return OtapiLogEntryInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiLogEntryInfo'
                )
            ) : array();
    }
}