<?php

class OtapiArrayOfItemRatingCollection extends BaseOtapiType{
    /**
     * @return OtapiItemRatingCollection[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
            $this->xmlData->Item,
            array(
                'type' => 'complexType',
                'name' => 'OtapiItemRatingCollection'
            )
        ) : array();
    }
}