<?php

class OtapiCreateTicketResponse extends BaseOtapiType{
    /**
     * @return OtapiCreateTicketAnswer
     */
    public function GetCreateTicketResult(){
        $value = isset($this->xmlData->CreateTicketResult) ? $this->xmlData->CreateTicketResult : false;
        return new OtapiCreateTicketAnswer($value);
    }
}