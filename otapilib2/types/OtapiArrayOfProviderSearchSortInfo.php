<?php

class OtapiArrayOfProviderSearchSortInfo extends BaseOtapiType{
    /**
     * @return OtapiProviderSearchSortInfo[]
     */
    public function GetSort(){
        return isset($this->xmlData->Sort) ? new UnboundedElementsIterator(
                $this->xmlData->Sort,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiProviderSearchSortInfo'
                )
            ) : array();
    }
}