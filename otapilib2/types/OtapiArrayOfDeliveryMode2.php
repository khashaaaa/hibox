<?php

class OtapiArrayOfDeliveryMode2 extends BaseOtapiType{
    /**
     * @return OtapiDeliveryMode[]
     */
    public function GetMode(){
        return isset($this->xmlData->Mode) ? new UnboundedElementsIterator(
                $this->xmlData->Mode,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiDeliveryMode'
                )
            ) : array();
    }
}