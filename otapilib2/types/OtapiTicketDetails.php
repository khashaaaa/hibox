<?php

class OtapiTicketDetails extends BaseOtapiType{
    /**
     * @return OtapiTicketInfo
     */
    public function GetTicketInfo(){
        $value = isset($this->xmlData->TicketInfo) ? $this->xmlData->TicketInfo : false;
        return new OtapiTicketInfo($value);
    }
    /**
     * @return OtapiArrayOfTicketMessage
     */
    public function GetTicketMessageList(){
        $value = isset($this->xmlData->TicketMessageList) ? $this->xmlData->TicketMessageList : false;
        return new OtapiArrayOfTicketMessage($value);
    }
}