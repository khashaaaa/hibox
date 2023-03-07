<?php

class OtapiArrayOfActionInfo1 extends BaseOtapiType{
    /**
     * @return OtapiActionInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiActionInfo'
                )
            ) : array();
    }
}