<?php

class OtapiArrayOfContentMenuItemInfo extends BaseOtapiType{
    /**
     * @return OtapiContentMenuItemInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
            $this->xmlData->Item,
            array(
                'type' => 'complexType',
                'name' => 'OtapiContentMenuItemInfo'
            )
        ) : array();
    }
}