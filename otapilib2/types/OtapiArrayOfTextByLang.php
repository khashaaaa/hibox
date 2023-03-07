<?php

class OtapiArrayOfTextByLang extends BaseOtapiType{
    /**
     * @return OtapiTextByLang[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiTextByLang'
                )
            ) : array();
    }
}