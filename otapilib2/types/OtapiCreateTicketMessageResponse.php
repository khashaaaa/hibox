<?php

class OtapiCreateTicketMessageResponse extends BaseOtapiType{
    /**
     * @return OtapiCreateTicketMessageAnswer
     */
    public function GetCreateTicketMessageResult(){
        $value = isset($this->xmlData->CreateTicketMessageResult) ? $this->xmlData->CreateTicketMessageResult : false;
        return new OtapiCreateTicketMessageAnswer($value);
    }
}