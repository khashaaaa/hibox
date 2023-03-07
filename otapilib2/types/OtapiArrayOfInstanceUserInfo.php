<?php

class OtapiArrayOfInstanceUserInfo extends BaseOtapiType{
    /**
     * @return OtapiInstanceUserInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiInstanceUserInfo'
                )
            ) : array();
    }
}