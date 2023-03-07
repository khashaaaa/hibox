<?php

class OtapiArrayOfCityInfo extends BaseOtapiType{
    /**
     * @return OtapiCityInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiCityInfo'
                )
            ) : array();
    }
}