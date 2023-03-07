<?php

class OtapiMoveItemFromCartToNoteResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetMoveItemFromCartToNoteResult(){
        $value = isset($this->xmlData->MoveItemFromCartToNoteResult) ? $this->xmlData->MoveItemFromCartToNoteResult : false;
        return new VoidOtapiAnswer($value);
    }
}