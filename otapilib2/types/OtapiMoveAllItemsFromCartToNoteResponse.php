<?php

class OtapiMoveAllItemsFromCartToNoteResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetMoveAllItemsFromCartToNoteResult(){
        $value = isset($this->xmlData->MoveAllItemsFromCartToNoteResult) ? $this->xmlData->MoveAllItemsFromCartToNoteResult : false;
        return new VoidOtapiAnswer($value);
    }
}