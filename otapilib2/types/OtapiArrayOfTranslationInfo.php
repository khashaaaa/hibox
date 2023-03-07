<?php

class OtapiArrayOfTranslationInfo extends BaseOtapiType{
    /**
     * @return OtapiTranslationInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiTranslationInfo'
                )
            ) : array();
    }
}