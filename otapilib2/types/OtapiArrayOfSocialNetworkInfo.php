<?php

class OtapiArrayOfSocialNetworkInfo extends BaseOtapiType{
    /**
     * @return OtapiSocialNetworkInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiSocialNetworkInfo'
                )
            ) : array();
    }
}