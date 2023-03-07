<?php

class OtapiArrayOfFieldValueMetaInfo extends BaseOtapiType{
    /**
     * @return OtapiFieldValueMetaInfo[]
     */
    public function GetValue(){
        return isset($this->xmlData->Value) ? new UnboundedElementsIterator(
                $this->xmlData->Value,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiFieldValueMetaInfo'
                )
            ) : array();
    }
}