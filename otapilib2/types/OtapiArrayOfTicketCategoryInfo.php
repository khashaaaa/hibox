<?php

class OtapiArrayOfTicketCategoryInfo extends BaseOtapiType{
    /**
     * @return OtapiTicketCategoryInfo[]
     */
    public function GetTicketCategoryInfo(){
        return isset($this->xmlData->TicketCategoryInfo) ? new UnboundedElementsIterator(
                $this->xmlData->TicketCategoryInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiTicketCategoryInfo'
                )
            ) : array();
    }
}