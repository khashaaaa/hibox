<?php

class OtapiArrayOfPriceFormationGroupInfo extends BaseOtapiType{
    /**
     * @return OtapiPriceFormationGroupInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiPriceFormationGroupInfo'
                )
            ) : array();
    }
}