<?php

class OtapiArrayOfProviderSearchMethodInfo extends BaseOtapiType{
    /**
     * @return OtapiProviderSearchMethodInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiProviderSearchMethodInfo'
                )
            ) : array();
    }
}