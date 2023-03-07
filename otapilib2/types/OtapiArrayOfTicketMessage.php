<?php

class OtapiArrayOfTicketMessage extends BaseOtapiType{
    /**
     * @return OtapiTicketMessage[]
     */
    public function GetTicketMessage(){
        return isset($this->xmlData->TicketMessage) ? new UnboundedElementsIterator(
                $this->xmlData->TicketMessage,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiTicketMessage'
                )
            ) : array();
    }
}