<?php

class OtapiArrayOfArchiveRecord extends BaseOtapiType{
    /**
     * @return OtapiArchiveRecord[]
     */
    public function GetArchiveRecord(){
        return isset($this->xmlData->ArchiveRecord) ? new UnboundedElementsIterator(
                $this->xmlData->ArchiveRecord,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiArchiveRecord'
                )
            ) : array();
    }
}