<?php

class OtapiTicketInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiArrayOfTicketInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiArrayOfTicketInfo($value);
    }
}