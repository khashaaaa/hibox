<?php

class OtapiArrayOfTicketInfo extends BaseOtapiType{
    /**
     * @return OtapiTicketInfo[]
     */
    public function GetTicketInfo(){
        return isset($this->xmlData->TicketInfo) ? new UnboundedElementsIterator(
                $this->xmlData->TicketInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiTicketInfo'
                )
            ) : array();
    }
}