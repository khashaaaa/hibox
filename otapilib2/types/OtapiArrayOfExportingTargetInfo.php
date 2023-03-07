<?php

class OtapiArrayOfExportingTargetInfo extends BaseOtapiType{
    /**
     * @return OtapiExportingTargetInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiExportingTargetInfo'
                )
            ) : array();
    }
}