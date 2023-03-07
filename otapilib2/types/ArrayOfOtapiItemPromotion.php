<?php

class ArrayOfOtapiItemPromotion extends BaseOtapiType{
    /**
     * @return OtapiItemPromotion[]
     */
    public function GetOtapiItemPromotion(){
        return isset($this->xmlData->OtapiItemPromotion) ? new UnboundedElementsIterator(
                $this->xmlData->OtapiItemPromotion,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiItemPromotion'
                )
            ) : array();
    }
}