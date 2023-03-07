<?php

class OtapiSearchBillsResponse extends BaseOtapiType{
    /**
     * @return OtapiBillInfoListAnswer
     */
    public function GetSearchBillsResult(){
        $value = isset($this->xmlData->SearchBillsResult) ? $this->xmlData->SearchBillsResult : false;
        return new OtapiBillInfoListAnswer($value);
    }
}