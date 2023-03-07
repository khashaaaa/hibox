<?php

class OtapiArrayOfEventInfo extends BaseOtapiType{
    /**
     * @return OtapiEventInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiEventInfo'
                )
            ) : array();
    }
}