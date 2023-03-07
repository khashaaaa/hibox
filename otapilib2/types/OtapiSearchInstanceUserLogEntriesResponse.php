<?php

class OtapiSearchInstanceUserLogEntriesResponse extends BaseOtapiType{
    /**
     * @return OtapiLogEntryInfoListFrameAnswer
     */
    public function GetSearchInstanceUserLogEntriesResult(){
        $value = isset($this->xmlData->SearchInstanceUserLogEntriesResult) ? $this->xmlData->SearchInstanceUserLogEntriesResult : false;
        return new OtapiLogEntryInfoListFrameAnswer($value);
    }
}