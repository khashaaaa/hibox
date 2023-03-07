<?php

class ArrayOfOtapiBoxStatistics extends BaseOtapiType{
    /**
     * @return OtapiBoxStatistics[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiBoxStatistics'
                )
            ) : array();
    }
}