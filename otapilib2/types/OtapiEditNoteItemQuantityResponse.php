<?php

class OtapiEditNoteItemQuantityResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetEditNoteItemQuantityResult(){
        $value = isset($this->xmlData->EditNoteItemQuantityResult) ? $this->xmlData->EditNoteItemQuantityResult : false;
        return new VoidOtapiAnswer($value);
    }
}