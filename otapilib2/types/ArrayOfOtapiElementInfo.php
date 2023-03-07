<?php

class ArrayOfOtapiElementInfo extends BaseOtapiType{
    /**
     * @return OtapiElementInfo[]
     */
    public function GetElementInfo(){
        return isset($this->xmlData->ElementInfo) ? new UnboundedElementsIterator(
                $this->xmlData->ElementInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiElementInfo'
                )
            ) : array();
    }
}