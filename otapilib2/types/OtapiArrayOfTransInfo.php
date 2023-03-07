<?php

class OtapiArrayOfTransInfo extends BaseOtapiType{
    /**
     * @return OtapiTransInfo[]
     */
    public function GetTransInfo(){
        return isset($this->xmlData->TransInfo) ? new UnboundedElementsIterator(
                $this->xmlData->TransInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiTransInfo'
                )
            ) : array();
    }
}