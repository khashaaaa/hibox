<?php

class OtapiArrayOfInstanceRoleRightInfo extends BaseOtapiType{
    /**
     * @return OtapiInstanceRoleRightInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiInstanceRoleRightInfo'
                )
            ) : array();
    }
}