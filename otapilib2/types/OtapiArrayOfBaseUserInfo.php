<?php

class OtapiArrayOfBaseUserInfo extends BaseOtapiType{
    /**
     * @return OtapiBaseUserInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiBaseUserInfo'
                )
            ) : array();
    }
}