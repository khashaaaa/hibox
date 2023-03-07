<?php

class OtapiEditNoteItemFieldsResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetEditNoteItemFieldsResult(){
        $value = isset($this->xmlData->EditNoteItemFieldsResult) ? $this->xmlData->EditNoteItemFieldsResult : false;
        return new VoidOtapiAnswer($value);
    }
}