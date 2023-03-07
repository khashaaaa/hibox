<?php

class OtapiGetTicketDetailsResponse extends BaseOtapiType{
    /**
     * @return OtapiTicketDetailsAnswer
     */
    public function GetGetTicketDetailsResult(){
        $value = isset($this->xmlData->GetTicketDetailsResult) ? $this->xmlData->GetTicketDetailsResult : false;
        return new OtapiTicketDetailsAnswer($value);
    }
}