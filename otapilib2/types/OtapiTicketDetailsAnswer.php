<?php

class OtapiTicketDetailsAnswer extends OtapiAnswer{
    /**
     * @return OtapiTicketDetails
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiTicketDetails($value);
    }
}