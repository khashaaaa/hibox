<?php

class OtapiMoveItemFromNoteToBasketResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetMoveItemFromNoteToBasketResult(){
        $value = isset($this->xmlData->MoveItemFromNoteToBasketResult) ? $this->xmlData->MoveItemFromNoteToBasketResult : false;
        return new VoidOtapiAnswer($value);
    }
}