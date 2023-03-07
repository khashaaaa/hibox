<?php

class OtapiArrayOfMetaEntityInfo extends BaseOtapiType{
    /**
     * @return OtapiMetaEntityInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiMetaEntityInfo'
                )
            ) : array();
    }
}