<?php

class OtapiGetTicketCatogoriesResponse extends BaseOtapiType{
    /**
     * @return OtapiTicketCategoryInfoListAnswer
     */
    public function GetGetTicketCatogoriesResult(){
        $value = isset($this->xmlData->GetTicketCatogoriesResult) ? $this->xmlData->GetTicketCatogoriesResult : false;
        return new OtapiTicketCategoryInfoListAnswer($value);
    }
}