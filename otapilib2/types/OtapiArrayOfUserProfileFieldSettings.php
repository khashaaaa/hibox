<?php

class OtapiArrayOfUserProfileFieldSettings extends BaseOtapiType{
    /**
     * @return OtapiUserProfileFieldSettings[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiUserProfileFieldSettings'
                )
            ) : array();
    }
}