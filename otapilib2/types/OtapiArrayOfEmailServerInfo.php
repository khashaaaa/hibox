<?php

class OtapiArrayOfEmailServerInfo extends BaseOtapiType{
    /**
     * @return OtapiEmailServerInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiEmailServerInfo'
                )
            ) : array();
    }
}