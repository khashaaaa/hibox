<?php

class ArrayOfOtapiPackageAdminInfo extends BaseOtapiType{
    /**
     * @return OtapiPackageAdminInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiPackageAdminInfo'
                )
            ) : array();
    }
}