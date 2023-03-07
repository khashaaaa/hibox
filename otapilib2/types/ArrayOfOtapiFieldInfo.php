<?php

class ArrayOfOtapiFieldInfo extends BaseOtapiType{
    /**
     * @return OtapiFieldInfo[]
     */
    public function GetFieldInfo(){
        return isset($this->xmlData->FieldInfo) ? new UnboundedElementsIterator(
                $this->xmlData->FieldInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiFieldInfo'
                )
            ) : array();
    }
}