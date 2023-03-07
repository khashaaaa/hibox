<?php

class OtapiArrayOfCountryInfo extends BaseOtapiType{
    /**
     * @return OtapiCountryInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiCountryInfo'
                )
            ) : array();
    }
}