<?php

class OtapiArrayOfPackageStatusInfo extends BaseOtapiType{
    /**
     * @return OtapiPackageStatusInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiPackageStatusInfo'
                )
            ) : array();
    }
}