<?php

class OtapiMetaInfo extends BaseOtapiType{
    /**
     * @return OtapiFieldMetaInfo[]
     */
    public function GetField(){
        return isset($this->xmlData->Field) ? new UnboundedElementsIterator(
                $this->xmlData->Field,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiFieldMetaInfo'
                )
            ) : array();
    }
}