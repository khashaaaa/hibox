<?php

class ArrayOfOtapiItemReview extends BaseOtapiType{
    /**
     * @return OtapiItemReview[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiItemReview'
                )
            ) : array();
    }
}