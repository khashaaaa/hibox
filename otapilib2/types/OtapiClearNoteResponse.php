<?php

class OtapiClearNoteResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetClearNoteResult(){
        $value = isset($this->xmlData->ClearNoteResult) ? $this->xmlData->ClearNoteResult : false;
        return new VoidOtapiAnswer($value);
    }
}