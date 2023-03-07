<?php

class OtapiAddItemToNoteResponse extends BaseOtapiType{
    /**
     * @return OtapiElementIdAnswer
     */
    public function GetAddItemToNoteResult(){
        $value = isset($this->xmlData->AddItemToNoteResult) ? $this->xmlData->AddItemToNoteResult : false;
        return new OtapiElementIdAnswer($value);
    }
}