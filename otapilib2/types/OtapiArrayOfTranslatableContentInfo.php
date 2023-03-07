<?php

class OtapiArrayOfTranslatableContentInfo extends BaseOtapiType{
    /**
     * @return OtapiTranslatableContentInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiTranslatableContentInfo'
                )
            ) : array();
    }
}