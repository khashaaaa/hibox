<?php

class OtapiGetCallArchivesResponse extends BaseOtapiType{
    /**
     * @return OtapiCallArchiveAnswer
     */
    public function GetGetCallArchivesResult(){
        $value = isset($this->xmlData->GetCallArchivesResult) ? $this->xmlData->GetCallArchivesResult : false;
        return new OtapiCallArchiveAnswer($value);
    }
}