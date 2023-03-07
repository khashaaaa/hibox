<?php

class OtapiArrayOfBasketCheckingMessage extends BaseOtapiType{
    /**
     * @return OtapiBasketCheckingMessage[]
     */
    public function GetMessage(){
        return isset($this->xmlData->Message) ? new UnboundedElementsIterator(
                $this->xmlData->Message,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiBasketCheckingMessage'
                )
            ) : array();
    }
}