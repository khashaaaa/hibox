<?php

class OtapiArrayOfFeatureInfo extends BaseOtapiType{
    /**
     * @return OtapiFeatureInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiFeatureInfo'
                )
            ) : array();
    }
}