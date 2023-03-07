<?php

class OtapiArrayOfBackgroundActivityInfo extends BaseOtapiType{
    /**
     * @return OtapiBackgroundActivityInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiBackgroundActivityInfo'
                )
            ) : array();
    }
}