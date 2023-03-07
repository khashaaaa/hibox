<?php

class OtapiArrayOfCollectionSummary extends BaseOtapiType{
    /**
     * @return OtapiCollectionSummary[]
     */
    public function GetCollectionSummary(){
        return isset($this->xmlData->CollectionSummary) ? new UnboundedElementsIterator(
                $this->xmlData->CollectionSummary,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiCollectionSummary'
                )
            ) : array();
    }
}