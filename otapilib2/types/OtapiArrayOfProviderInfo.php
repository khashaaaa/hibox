<?php

class OtapiArrayOfProviderInfo extends BaseOtapiType{
    /**
     * @return OtapiProviderInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiProviderInfo'
                )
            ) : array();
    }
}