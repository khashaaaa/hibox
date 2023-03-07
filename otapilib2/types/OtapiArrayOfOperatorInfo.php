<?php

class OtapiArrayOfOperatorInfo extends BaseOtapiType{
    /**
     * @return OtapiOperatorInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiOperatorInfo'
                )
            ) : array();
    }
}