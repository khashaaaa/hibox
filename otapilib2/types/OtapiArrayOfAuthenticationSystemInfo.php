<?php

class OtapiArrayOfAuthenticationSystemInfo extends BaseOtapiType{
    /**
     * @return OtapiAuthenticationSystemInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiAuthenticationSystemInfo'
                )
            ) : array();
    }
}