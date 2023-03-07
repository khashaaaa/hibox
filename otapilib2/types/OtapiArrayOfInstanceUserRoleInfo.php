<?php

class OtapiArrayOfInstanceUserRoleInfo extends BaseOtapiType{
    /**
     * @return OtapiInstanceUserRoleInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiInstanceUserRoleInfo'
                )
            ) : array();
    }
}