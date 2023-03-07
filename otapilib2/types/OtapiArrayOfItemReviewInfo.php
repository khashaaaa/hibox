<?php

class OtapiArrayOfItemReviewInfo extends BaseOtapiType{
    /**
     * @return OtapiItemReviewInfo[]
     */
    public function GetItem(){
        return isset($this->xmlData->Item) ? new UnboundedElementsIterator(
                $this->xmlData->Item,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiItemReviewInfo'
                )
            ) : array();
    }
}