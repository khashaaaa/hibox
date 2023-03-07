<?php

class OtapiArrayOfPackageTrackingCheckpoint extends BaseOtapiType{
    /**
     * @return OtapiPackageTrackingCheckpoint[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiPackageTrackingCheckpoint'
                )
            ) : array();
    }
}