<?php

class OtapiGetNoteResponse extends BaseOtapiType{
    /**
     * @return OtapiCollectionInfoAnswer
     */
    public function GetGetNoteResult(){
        $value = isset($this->xmlData->GetNoteResult) ? $this->xmlData->GetNoteResult : false;
        return new OtapiCollectionInfoAnswer($value);
    }
}