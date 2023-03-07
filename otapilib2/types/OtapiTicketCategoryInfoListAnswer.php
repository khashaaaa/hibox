<?php

class OtapiTicketCategoryInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiArrayOfTicketCategoryInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiArrayOfTicketCategoryInfo($value);
    }
}