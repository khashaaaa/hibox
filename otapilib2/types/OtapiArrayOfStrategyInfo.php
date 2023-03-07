<?php

class OtapiArrayOfStrategyInfo extends BaseOtapiType{
    /**
     * @return OtapiStrategyInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiStrategyInfo'
                )
            ) : array();
    }
}