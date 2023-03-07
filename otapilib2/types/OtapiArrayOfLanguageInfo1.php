<?php

class OtapiArrayOfLanguageInfo1 extends BaseOtapiType{
    /**
     * @return OtapiLanguageInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiLanguageInfo'
                )
            ) : array();
    }
}