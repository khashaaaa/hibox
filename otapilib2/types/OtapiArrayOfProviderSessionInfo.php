<?php

class OtapiArrayOfProviderSessionInfo extends BaseOtapiType{
    /**
     * @return OtapiProviderSessionInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiProviderSessionInfo'
                )
            ) : array();
    }
}