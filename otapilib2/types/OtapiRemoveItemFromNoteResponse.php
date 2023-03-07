<?php

class OtapiRemoveItemFromNoteResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetRemoveItemFromNoteResult(){
        $value = isset($this->xmlData->RemoveItemFromNoteResult) ? $this->xmlData->RemoveItemFromNoteResult : false;
        return new VoidOtapiAnswer($value);
    }
}