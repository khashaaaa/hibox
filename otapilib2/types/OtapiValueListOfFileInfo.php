<?php

class OtapiValueListOfFileInfo extends BaseOtapiType{
    /**
     * @return OtapiFileInfo[]
     */
    public function GetValue(){
        return isset($this->xmlData->Value) ? new UnboundedElementsIterator(
                $this->xmlData->Value,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiFileInfo'
                )
            ) : array();
    }
}