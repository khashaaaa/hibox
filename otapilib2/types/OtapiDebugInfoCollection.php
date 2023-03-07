<?php

class OtapiDebugInfoCollection extends BaseOtapiType{
    /**
     * @return OtapiDebugInfoRecord[]
     */
    public function GetRecord(){
        return isset($this->xmlData->Record) ? new UnboundedElementsIterator(
                $this->xmlData->Record,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiDebugInfoRecord'
                )
            ) : array();
    }
}