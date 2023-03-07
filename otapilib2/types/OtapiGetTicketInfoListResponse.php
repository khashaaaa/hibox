<?php

class OtapiGetTicketInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiTicketInfoListAnswer
     */
    public function GetGetTicketInfoListResult(){
        $value = isset($this->xmlData->GetTicketInfoListResult) ? $this->xmlData->GetTicketInfoListResult : false;
        return new OtapiTicketInfoListAnswer($value);
    }
}